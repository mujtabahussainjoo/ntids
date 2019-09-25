<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

abstract class DefaultLog extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_objectManager;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('actionsLogGrid');
    }

    public function decorateStatus($value, $row, $column)
    {
        return '<span class="amaudit-' . $value . '">' . $value . '</span>';
    }


    public function showOpenElementUrl($value, $row, $column)
    {
        $category = $row->getCategory();
        $url = '';
        $param = ($row->getParametrName() == 'back' || $row->getParametrName() == 'underfined') ? 'id' : $row->getParametrName();
        if ($row->getElementId() && $category && $row->getType() != 'Delete'
            && ($category == 'catalog/product'
                || $category == 'customer'
                || $category == 'customer/index'
                || $category == 'catalog_product_attribute')
                || $category == 'customer/group'
                || $category == 'catalog/product_attribute'
                || $category == 'sales/order_create'
                || $category == 'sales/order'
                || $category == 'admin/order_shipment'
                || $category == 'sales/order_creditmemo'
                || $category == 'sales/order_invoice'
                || $category == 'catalog_rule/promo_catalog'
        )
        {
            if ($category == 'sales/order_create'
                || $category == 'sales/order'
                || $category == 'admin/order_shipment'
                || $category == 'sales/order_creditmemo'
                || $category == 'sales/order_invoice'
            ) {
                $url = $this->getUrl('sales/order/view', array('order_id' => $row->getElementId()));
            } else {
                if ($category == 'customer') {
                    $category = 'customer/index';
                }
                $url = $this->getUrl($category . '/edit', array($param => $row->getElementId()));
            }
        }

        $view = "";
        if ($url) $view = '&nbsp<a href="' . $url . '"><span>[' . __('view') . ']</span></a>';

        return '<span>' . $value . '</span>' . $view;
    }

    public function showActions ($value, $row, $column)
    {
        $preview = "";

        if (($row->getType() == "Edit" || $row->getType() == "New" || $row->getType() == 'Restore')) {
            $preview = '<a class="amaudit-preview"
            onclick="previewChanges.open(\'' . $this->_backendHelper->getUrl('amaudit/actionslog/preview') . '\', \'' . $row->getId() . '\');">'
                . __('Preview Changes') . '</a><span id="' . $row->getId() . '_editor""></span><br>';
        }

        return $preview . '<a href="' . $this->getUrl('amaudit/actionslog/edit', array('id' => $row->getId())) . '"><span>' . __('View Details') . '</span></a>';
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit', ['id' => $row->getId()]
        );
    }
}
