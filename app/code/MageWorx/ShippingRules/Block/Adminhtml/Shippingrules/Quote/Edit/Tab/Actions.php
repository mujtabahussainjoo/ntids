<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Quote\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use MageWorx\ShippingRules\Model\Config\Source\Shipping\ExtendedActions as ShippingActionsConfig;
use MageWorx\ShippingRules\Model\Config\Source\Shipping\Methods as Config;
use MageWorx\ShippingRules\Model\Rule;

class Actions extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    const HIDDEN_FIELDSET_CLASS_NAME = 'hidden-fieldset';

    /**
     * Core registry
     *
     * @var Fieldset
     */
    protected $rendererFieldset;

    /** @var Config */
    protected $shippingConfig;

    /** @var Config */
    protected $shippingActionsConfig;

    /** @var \Magento\Framework\Convert\DataObject */
    protected $objectConverter;

    /** @var Yesno */
    protected $yesNoConfig;

    /** @var \MageWorx\ShippingRules\Model\Rule */
    protected $sourceModel;

    protected $dataFormPart = 'shippingrules_quote_form';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Fieldset $rendererFieldset
     * @param Config $config
     * @param ObjectConverter $objectConverter
     * @param ShippingActionsConfig $shippingActionsConfig
     * @param Yesno $yesno
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Fieldset $rendererFieldset,
        Config $config,
        ObjectConverter $objectConverter,
        ShippingActionsConfig $shippingActionsConfig,
        Yesno $yesno,
        array $data = []
    )
    {
        $this->rendererFieldset = $rendererFieldset;
        $this->shippingConfig = $config;
        $this->shippingActionsConfig = $shippingActionsConfig;
        $this->objectConverter = $objectConverter;
        $this->yesNoConfig = $yesno;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare amounts for form
     *
     * @param Rule $model
     * @return Rule
     */
    protected function _prepareModel(Rule $model)
    {
        $amounts = $model->getAmount();

        if (empty($amounts)) {
            return $model;
        }

        foreach ($amounts as $amountKey => $amountData) {
            $valueKey = 'amount_' . $amountKey . '_value';
            $value = (float)$amountData['value'] * 1;
            $model->setData($valueKey, $value);

            $sortOrderKey = 'amount_' . $amountKey . '_sort';
            $sortOrderValue = (int)$amountData['sort'] * 1;
            $model->setData($sortOrderKey, $sortOrderValue);
        }

        return $model;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Actions|\Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_quote_rule');
        $model = $this->_prepareModel($model);
        $this->sourceModel = $model;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $this->addMainFieldset($form);
        $this->addModifyCostFieldset($form);
        $this->addHideShippingMethodsFieldset($form);
        $this->addChangeShippingMethodsDataFieldset($form);

        $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('mageworx_shippingrules/shippingrules_quote/newActionHtml/form/rule_actions_fieldset')
        );

        $this->_eventManager->dispatch('adminhtml_block_shippingrules_actions_prepareform', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Add the fieldset with ability to hide selected shipping methods
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addHideShippingMethodsFieldset($form)
    {
        $modelActionType = $this->getSourceModelActionType();
        $hidden = !in_array(Rule::ACTION_DISABLE_SM, $modelActionType) ? self::HIDDEN_FIELDSET_CLASS_NAME : '';
        $classes = 'dependable_fieldset_' . Rule::ACTION_DISABLE_SM . ' ' . $hidden;
        $hideSMFieldset = $form->addFieldset(
            'hide_shipping_method_fieldset',
            ['legend' => __('Hide Shipping Method'), 'class' => $classes]
        );

        $shippingmethods = $this->getShippingMethods();
        $hideSMFieldset->addField(
            'disabled_shipping_methods',
            'multiselect',
            [
                'name' => 'disabled_shipping_methods[]',
                'label' => __('Disabled Shipping Methods'),
                'title' => __('Disabled Shipping Methods'),
                'values' => $shippingmethods,
            ]
        );

        $hideSMFieldset->addField(
            'display_error_message',
            'checkboxes',
            [
                'name' => 'display_error_message',
                'label' => __('Display Error Message'),
                'title' => __('Display Error Message'),
                'onchange' => 'changeActions(this)',
                'required' => false,
                'checked' => [$this->sourceModel->getDisplayErrorMessage()],
                'values' => [
                    ['value' => '1', 'label' => __('Display Error Message')],
                ],
                'note' => __('If selected, the error message will be displayed instead of a disabled delivery method'),
            ]
        );

        $errorMessageNote = 'Error message for the disabled delivery methods. ' .
            'You can use the variables {{carrier_title}} and {{method_title}}' .
            ' which will be replaced with a corresponding values of the method.';
        $hideSMFieldset->addField(
            'error_message',
            'textarea',
            [
                'name' => 'error_message',
                'label' => __('Error Message'),
                'title' => __('Error Message'),
                'note' => __($errorMessageNote),
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addStoreSpecificErrorMessageFieldset($hideSMFieldset);
        }

        return $hideSMFieldset;
    }

    /**
     * Adds sub-fieldset with store specific error messages
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $hideSMFieldset
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addStoreSpecificErrorMessageFieldset(\Magento\Framework\Data\Form\Element\Fieldset $hideSMFieldset)
    {
        $hidden = !$this->sourceModel->getDisplayErrorMessage() ? self::HIDDEN_FIELDSET_CLASS_NAME : '';
        $errorMessages = $this->sourceModel->getStoreErrmsgs();
        $classes = 'dependable_fieldset_1 ' . $hidden;
        $storeSpecificErrorMessageFieldset = $hideSMFieldset->addField(
            'display_error_message_fieldset',
            'fieldset',
            [
                'legend' => __('Store specific error messages for the disabled shipping methods.'),
                'class' => $classes,
            ]
        );

        $storeSpecificErrorMessageFieldset->addField(
            'note_label',
            'label',
            [
                'name' => 'note_label_1',
                'label' => __(' '),
                'title' => __(' '),
                'note' => __(__('You can use the variables {{carrier_title}} and {{method_title}}' .
                    ' which will be replaced with a corresponding values of the method.')),
            ]
        );

        /** @var \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset $renderer */
        $renderer = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');

        $storeSpecificErrorMessageFieldset->setRenderer($renderer);
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $fieldId = "w_{$website->getId()}_errmsg";
            $storeSpecificErrorMessageFieldset->addField(
                $fieldId,
                'note',
                [
                    'label' => $website->getName(),
                    'fieldset_html_class' => 'website',
                ]
            );

            $groups = $website->getGroups();
            foreach ($groups as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }

                $groupFieldId = "sg_{$group->getId()}_errmsg";
                $storeSpecificErrorMessageFieldset->addField(
                    $groupFieldId,
                    'note',
                    [
                        'label' => $group->getName(),
                        'fieldset_html_class' => 'store-group',
                    ]
                );

                foreach ($stores as $store) {
                    $id = $store->getId();
                    $storeFieldId = "s_{$id}_errmsg";
                    $storeFieldName = 'store_errmsgs[' . $id . ']';
                    $storeName = $store->getName();

                    if (isset($errorMessages[$id])) {
                        $storeValue = $errorMessages[$id];
                    } else {
                        $storeValue = '';
                    }


                    $this->sourceModel->setData($storeFieldId, $storeValue);
                    $storeSpecificErrorMessageFieldset->addField(
                        $storeFieldId,
                        'textarea',
                        [
                            'name' => $storeFieldName,
                            'title' => $storeName,
                            'label' => $storeName,
                            'required' => false,
                            'value' => $storeValue,
                            'fieldset_html_class' => 'store',
                            'data-form-part' => $this->dataFormPart,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Add the modify cost fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addModifyCostFieldset($form)
    {

        $modelActionType = $this->getSourceModelActionType();
        $hidden = !in_array(Rule::ACTION_OVERWRITE_COST, $modelActionType) ? self::HIDDEN_FIELDSET_CLASS_NAME : '';
        $classes = 'dependable_fieldset_' . Rule::ACTION_OVERWRITE_COST . ' ' . $hidden;
        $modifyCostFieldset = $form->addFieldset(
            'modify_cost_fieldset',
            ['legend' => __('Modify Shipping Cost'), 'class' => $classes]
        );

        // How calculate
        $actions = $this->shippingActionsConfig->toOptionArray();
        $simpleActionField = $modifyCostFieldset->addField(
            'simple_action',
            'multiselect',
            [
                'label' => __('Modifier'),
                'name' => 'simple_action',
                'onchange' => 'changeAmounts(this)',
                'values' => $actions,
                'note' => __('Note: the "per Item" action uses each row in customers cart as multiplier (5 same products
                                in one row and 2 same items in another row = 2 qty), the "per Product" action uses an
                                each valid items qty as multiplier (5 same products in one row and 2 same items in
                                another row = 7 qty).'),
            ]
        );

        $simpleActionField->setAfterElementHtml("<script>
            require(['jquery', 'jquery/ui'], function($){
                $('#rule_simple_action').trigger('change');
            });

            function changeAmounts(e) {

                var values = jQuery(e).val();

                try {
                    jQuery('.amount-field').each(function() {
                        jQuery(this).closest('.field').hide();
                    });

                    jQuery(values).each(function(){
                        var className = '.amount-field.'+this;
                        jQuery(className).closest('.field').show();
                    });
                } catch (e) {
                    console.log(e);
                }
            }
        </script>");

        $this->_addAmountFields($actions, $modifyCostFieldset, $this->sourceModel);

        // All available shipping methods
        $shippingmethods = $this->getShippingMethods();
        $modifyCostFieldset->addField(
            'shipping_methods',
            'multiselect',
            [
                'name' => 'shipping_methods[]',
                'label' => __('Apply to Shipping Methods'),
                'title' => __('Apply to Shipping Methods'),
                'values' => $shippingmethods,
            ]
        );

        return $modifyCostFieldset;
    }

    /**
     * Add the change data fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addChangeShippingMethodsDataFieldset($form)
    {
        $modelActionType = $this->getSourceModelActionType();
        $hidden = !in_array(Rule::ACTION_CHANGE_SM_DATA, $modelActionType) ?
            self::HIDDEN_FIELDSET_CLASS_NAME :
            '';
        $classes = 'dependable_fieldset_' . Rule::ACTION_CHANGE_SM_DATA . ' ' . $hidden;

        $fieldset = $form->addFieldset(
            'change_shipping_method_fieldset',
            ['legend' => __('Change Shipping Method Data'), 'class' => $classes],
            false,
            true
        );

        $changeTitleField = $fieldset->addField(
            'changed_titles',
            'select',
            [
                'name' => 'changed_titles',
                'label' => __('Change Title'),
                'title' => __('Change Title'),
            ]
        );

        $layout = $this->getLayout();
        $renderer = $layout->createBlock(
            'MageWorx\ShippingRules\Block\Adminhtml\Form\Field\RuleMethodsTitle'
        );

        $changeTitleField->setRenderer($renderer);

        return $fieldset;
    }

    /**
     * Add main fieldset with action type and stop future rules processing select.
     *
     * @param \Magento\Framework\Data\Form $form
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function addMainFieldset($form)
    {

        $mainFieldset = $form->addFieldset(
            'action_fieldset',
            ['legend' => __('Rule\'s Action')]
        );

        // What to do
        $actionType = $mainFieldset->addField(
            'action_type',
            'checkboxes',
            [
                'label' => __('Type'),
                'name' => 'action_type[]',
                'required' => false,
                'onchange' => 'changeActions(this)',
                'values' => [
                    ['value' => Rule::ACTION_OVERWRITE_COST, 'label' => __('Modify Shipping Cost')],
                    ['value' => Rule::ACTION_DISABLE_SM, 'label' => __('Hide Shipping Method')],
                    ['value' => Rule::ACTION_CHANGE_SM_DATA, 'label' => __('Change Data')],
                ],
            ]
        );

        $actionType->setAfterElementHtml("<script>
        function changeActions(selectItem){
            var item = jQuery(selectItem);
            var targetClass = '.dependable_fieldset_'+item.val();
            var target = jQuery(targetClass);
            target.toggleClass('hidden-fieldset');
        }
        </script>");

        // Stop future rules processing
        $mainFieldset->addField(
            'stop_rules_processing',
            'select',
            [
                'label' => __('Stop Further Processing'),
                'title' => __('Stop Further Processing'),
                'name' => 'stop_rules_processing',
                'options' => $this->yesNoConfig->toArray(),
            ]
        );

        return $mainFieldset;
    }

    /**
     * Add fields
     *
     * @param $data
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param Rule $model
     * @param string $parentLabel
     */
    protected function _addAmountFields(
        $data,
        \Magento\Framework\Data\Form\Element\Fieldset $fieldset,
        Rule $model,
        $parentLabel = ''
    )
    {

        foreach ($data as $action) {
            if (empty($action['value'])) {
                continue;
            }

            if (is_array($action['value'])) {
                $this->_addAmountFields($action['value'], $fieldset, $model, $action['label']);
            } else {
                $classes = ['validate-not-negative-number', 'hidden-field', 'amount-field', $action['value']];
                $class = implode(' ', $classes);
                $label = $action['label'];
                if ($parentLabel) {
                    $label = $parentLabel . ' [' . $label . ']';
                }
                $fieldset->addField(
                    'amount_' . $action['value'] . '_value',
                    'text',
                    [
                        'name' => 'amount[' . $action['value'] . '][value]',
                        'required' => false,
                        'class' => $class,
                        'label' => $label,
                    ]
                );
                $fieldset->addField(
                    'amount_' . $action['value'] . '_sort',
                    'text',
                    [
                        'name' => 'amount[' . $action['value'] . '][sort]',
                        'required' => false,
                        'class' => $class,
                        'label' => 'Sort order',
                    ]
                );
            }
        }
    }

    /**
     * Return source model action type or empty array
     *
     * @return array
     */
    protected function getSourceModelActionType()
    {
        $model = $this->sourceModel;
        $modelActionType = $model->getActionType() ? $model->getActionType() : [];

        return $modelActionType;
    }

    /**
     * Return all shipping methods as option array
     *
     * @return array
     */
    protected function getShippingMethods()
    {
        return $this->shippingConfig->toOptionArray();
    }
}
