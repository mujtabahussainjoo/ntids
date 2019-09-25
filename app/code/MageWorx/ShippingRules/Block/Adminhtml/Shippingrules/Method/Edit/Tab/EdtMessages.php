<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Method\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Data\Form;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\EditTabLabels;
use MageWorx\ShippingRules\Model\Carrier\Method;
use MageWorx\ShippingRules\Ui\DataProvider\Method\Form\Modifier\AbstractModifier as MethodModifier;

/**
 * Class EdtMessages
 */
class EdtMessages extends EditTabLabels
{
    const DEFAULT_TAB_LABEL = 'Estimated Delivery Time Messages';
    const DEFAULT_TAB_TITLE = 'Estimated Delivery Time Messages';

    /**
     * @var string
     */
    protected $dataFormPart;

    /**
     * @var string
     */
    protected $_nameInLayout = 'store_view_edt_store_specific_messages';

    /**
     * @var MethodRepositoryInterface
     */
    protected $methodRepository;

    /**
     * Initialize dependencies.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param MethodRepositoryInterface $methodRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        MethodRepositoryInterface $methodRepository,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->methodRepository = $methodRepository;
        $this->dataFormPart = MethodModifier::FORM_NAME;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Method $method */
        $method = $this->resolveMethodEntity();
        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('method_');

        if (!$this->_storeManager->isSingleStoreMode()) {
            $messages = $method->getEdtStoreSpecificMessages();
            $this->_createStoreSpecificFieldset($form, $messages);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Is need to show this tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            return false;
        }

        return true;
    }

    /**
     * Is tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get current tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        $label = static::DEFAULT_TAB_LABEL;

        return __($label);
    }

    /**
     * Get default tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        $title = static::DEFAULT_TAB_TITLE;

        return __($title);
    }

    /**
     * Get default tab class
     *
     * @return null
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * Get tab url
     *
     * @return null
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * Is tab loaded via ajax
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Create store specific fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $messages
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected function _createStoreSpecificFieldset($form, $messages)
    {
        $fieldset = $form->addFieldset(
            'edt_store_specific_messages_fieldset',
            [
                'legend' => __('Store View Specific Estimated Delivery Time Messages'),
                'class' => 'store-scope',
            ]
        );

        /** @var \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset $renderer */
        $renderer = $this->getLayout()
            ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset');

        $fieldset->setRenderer($renderer);
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $fieldId = "w_{$website->getId()}_edt_store_specific_message";
            $fieldset->addField(
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

                $groupFieldId = "sg_{$group->getId()}_edt_store_specific_message";
                $fieldset->addField(
                    $groupFieldId,
                    'note',
                    [
                        'label' => $group->getName(),
                        'fieldset_html_class' => 'store-group',
                    ]
                );

                foreach ($stores as $store) {
                    $id = $store->getId();
                    $storeFieldId = "s_{$id}";
                    $storeFieldName = 'edt_store_specific_messages[' . $id . ']';
                    $storeName = $store->getName();

                    if (isset($messages[$id])) {
                        $storeValue = $messages[$id];
                    } else {
                        $storeValue = '';
                    }

                    $fieldset->addField(
                        $storeFieldId,
                        'text',
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

        return $fieldset;
    }

    /**
     * Find corresponding method or return a new one (empty from repo)
     *
     * @return Method
     */
    private function resolveMethodEntity()
    {
        /** @var Method $method */
        $method = $this->_coreRegistry->registry(Method::CURRENT_METHOD);
        if ($method) {
            return $method;
        }

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $method = $this->methodRepository->getById($id);
        } else {
            $method = $this->methodRepository->getEmptyEntity();
        }

        return $method;
    }
}
