<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Quote\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Locale\Weekdays;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class Main extends Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $objectConverter;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Weekdays
     */
    protected $daysOfWeek;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param ObjectConverter $objectConverter
     * @param Store $systemStore
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Weekdays $weekdays
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        ObjectConverter $objectConverter,
        Store $systemStore,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Weekdays $weekdays,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->objectConverter = $objectConverter;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->daysOfWeek = $weekdays;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Rule Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Rule Information');
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
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_promo_quote_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('General Information')]);

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Rule Name'), 'title' => __('Rule Name'), 'required' => true]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height: 100px;'
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => ['1' => __('Active'), '0' => __('Inactive')]
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Priority'),
                'note' => __('The Rule with a max priority (e.g. 999) will be applied first, then will be applied the
                                rule with a lower priority (e.g. 100). The Rule with the lowest priority (e.g. 0) will
                                be applied at the end.')
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_ids',
                'multiselect',
                [
                    'name' => 'store_ids[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_ids',
                'hidden',
                [
                    'name'      => 'store_ids[]',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ]
            );

            $model->setStoreIds($this->_storeManager->getStore(true)->getId());
        }

        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())
            ->getItems();
        $fieldset->addField(
            'customer_group_ids',
            'multiselect',
            [
                'name' => 'customer_group_ids[]',
                'label' => __('Customer Groups'),
                'title' => __('Customer Groups'),
                'required' => true,
                'values' =>  $this->objectConverter->toOptionArray($groups, 'id', 'code')
            ]
        );

        $daysOfWeek = $this->daysOfWeek->toOptionArray();
        $fieldset->addField(
            'days_of_week',
            'multiselect',
            [
                'name' => 'days_of_week',
                'label' => __('Available On'),
                'title' => __('Available On'),
                'values' => $daysOfWeek
            ]
        );

        $dateFormat = 'M/d/yy';//$this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From'),
                'title' => __('From'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To'),
                'title' => __('To'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );

        $useTimeTrigger = $fieldset->addField(
            'use_time',
            'checkboxes',
            [
                'name' => 'use_time',
                'label' => __('Use Time'),
                'title' => __('Use Time'),
                'onchange' => 'changeUseTime(this)',
                'values' => [
                    ['value' => '1', 'label' => __('Yes')]
                ],
                'checked' => (int)$model->getData('use_time')
            ]
        );

        $useTimeTrigger->setAfterElementHtml("<script>
            require(['jquery', 'jquery/ui'], function($){
                $('input[name=" . $useTimeTrigger->getName() . "]').trigger('change');
            });
            function changeUseTime(selectItem){
                var item = jQuery(selectItem);
                if (item.is(':checked')) {
                    jQuery('.field-time_range').show();
                    jQuery('.field-time_enabled').show();
                } else {
                    jQuery('.field-time_range').hide();
                    jQuery('.field-time_enabled').hide();
                }
            }
        </script>");

        $time = $fieldset->addField(
            'time',
            'time',
            [
                'name' => 'time',
                'label' => __('Time'),
                'title' => __('Time')
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Widget\TimeSlider'
        );
        /** @var \Magento\Framework\Data\Form\Element\Renderer\RendererInterface $renderer */
        $time->setRenderer($renderer);
        $time->getRenderer()->setRule($model);

        $fieldset->addField(
            'time_enabled',
            'radios',
            [
                'name' => 'time_enabled',
                'label' => __('Time when Rule is'),
                'title' => __('Time when Rule is'),
                'values' => [
                    1 => [
                        'value' => 1,
                        'label' => 'Functioning',
                    ],
                    2 => [
                        'value' => 0,
                        'label' => 'Not Functioning',
                    ]
                ]
            ]
        );

        if ($model->getData('days_of_week') === null) {
            $model->setData('days_of_week', '');
        }

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        $this->_eventManager->dispatch('adminhtml_shippingrules_quote_edit_tab_main_prepare_form', ['form' => $form]);

        return parent::_prepareForm();
    }
}
