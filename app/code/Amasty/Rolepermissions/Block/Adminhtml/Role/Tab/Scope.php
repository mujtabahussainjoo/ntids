<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Scope extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const MODE_NONE = 0;

    const MODE_SITE = 1;

    const MODE_VIEW = 2;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    private $optionList;

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    private $fieldFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Config\Model\Config\Source\Yesno $optionList,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->optionList = $optionList;
        $this->fieldFactory = $fieldFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced: Scope');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        if (!$model->getId()) {
            $model->setLimitOrders(true)
                ->setLimitInvoices(true)
                ->setLimitShipments(true)
                ->setLimitMemos(true);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('amrolepermissions_scope_fieldset', ['legend' => __('Choose Access Scope')]);

        $mode = $fieldset->addField(
            'scope_access_mode',
            'select',
            [
                'label'  => __('Limit Access To'),
                'id'     => 'scope_access_mode',
                'name'   => 'amrolepermissions[scope_access_mode]',
                'values' => [
                    self::MODE_NONE => __('Allow All Stores'),
                    self::MODE_SITE => __('Specified Websites'),
                    self::MODE_VIEW => __('Specified Store Views'),
                ],
            ]
        );

        $websites = $fieldset->addField(
            'scope_websites',
            'multiselect',
            [
                'name'   => 'amrolepermissions[scope_websites]',
                'label'  => __('Websites'),
                'title'  => __('Websites'),
                'values' => $this->_systemStore->getWebsiteValuesForForm()
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
        );
        $websites->setRenderer($renderer);

        $stores = $fieldset->addField(
            'scope_storeviews',
            'multiselect',
            [
                'name'   => 'amrolepermissions[scope_storeviews]',
                'label'  => __('Store Views'),
                'title'  => __('Store Views'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, false),
            ]
        );
        $stores->setRenderer($renderer);

        $limitOrders = $fieldset->addField(
            'limit_orders',
            'select',
            [
                'label'  => __('Limit Access To Orders'),
                'name'   => 'amrolepermissions[limit_orders]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $limitInvoices = $fieldset->addField(
            'limit_invoices',
            'select',
            [
                'label'  => __('Limit Access To Invoices And Transactions'),
                'name'   => 'amrolepermissions[limit_invoices]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $limitShipments = $fieldset->addField(
            'limit_shipments',
            'select',
            [
                'label'  => __('Limit Access To Shipments'),
                'name'   => 'amrolepermissions[limit_shipments]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $limitMemos = $fieldset->addField(
            'limit_memos',
            'select',
            [
                'label'  => __('Limit Access To Credit Memos'),
                'name'   => 'amrolepermissions[limit_memos]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $negativeNone = $this->fieldFactory->create(
            ['fieldData' => ['value' => (string)self::MODE_NONE, 'negative' => 1],  'fieldPrefix' => '']
        );
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )
            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap($websites->getHtmlId(), $websites->getName())
            ->addFieldMap($stores->getHtmlId(), $stores->getName())
            ->addFieldMap($limitOrders->getHtmlId(), $limitOrders->getName())
            ->addFieldMap($limitInvoices->getHtmlId(), $limitInvoices->getName())
            ->addFieldMap($limitShipments->getHtmlId(), $limitShipments->getName())
            ->addFieldMap($limitMemos->getHtmlId(), $limitMemos->getName())
            ->addFieldDependence(
                $websites->getName(),
                $mode->getName(),
                self::MODE_SITE
            )
            ->addFieldDependence(
                $stores->getName(),
                $mode->getName(),
                self::MODE_VIEW
            )
            ->addFieldDependence(
                $limitOrders->getName(),
                $mode->getName(),
                $negativeNone
            )
            ->addFieldDependence(
                $limitInvoices->getName(),
                $mode->getName(),
                $negativeNone
            )
            ->addFieldDependence(
                $limitShipments->getName(),
                $mode->getName(),
                $negativeNone
            )
            ->addFieldDependence(
                $limitMemos->getName(),
                $mode->getName(),
                $negativeNone
            )
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
