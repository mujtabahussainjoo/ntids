<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Block\Adminhtml\Report\Sales;

/**
 * Adminhtml sales report by category grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magedelight\MembershipSubscription\Block\Adminhtml\Grid\AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'membership_product_id';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        
        parent::_construct();
//        $this->setCountTotals(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return 'Magedelight\MembershipSubscription\Model\ResourceModel\Report\Order\Collection';
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        
        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);
        
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order Id'),
                'index' => 'increment_id',
                'sortable' => false,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );
        
        $this->addColumn(
            'status',
            [
                'header' => __('Order Status'),
                'index' => 'status',
                'totals_label' => __('Order Status'),
                'sortable' => false,
            ]
        );
        
        $this->addColumn(
            'customer_firstname',
            [
                'header' => __('First Name'),
                'index' => 'customer_firstname',
                'totals_label' => __('First Name'),
                'sortable' => false,
            ]
        );
        
        $this->addColumn(
            'customer_lastname',
            [
                'header' => __('Last Name'),
                'index' => 'customer_lastname',
                'totals_label' => __('Last Name'),
                'sortable' => false,
            ]
        );
        
        $this->addColumn(
            'base_grand_total',
            [
                'header' => __('Sales Total'),
                'index' => 'base_grand_total',
                'type' => 'currency',
                'total' => 'sum',
                'currency_code' => $currencyCode,
                'sortable' => false,
            ]
        );
        
        $this->addColumn(
            'total_discount',
            [
                'header' => __('Discount'),
                'type' => 'currency',
                'total' => 'sum',
                'currency_code' => $currencyCode,
                'index' => 'total_discount',
                'sortable' => false,
                'header_css_class' => 'col-sales-total',
                'column_css_class' => 'col-sales-total'
            ]
        );
        
        $this->addColumn(
            'total_qty_ordered',
            [
                'header' => __('Quantity'),
                'index' => 'total_qty_ordered',
                'sortable' => false,
                'type' => 'number',
                'total' => 'sum',
            ]
        );
        
        $this->addColumn(
            'shipping_amount',
            [
                'header' => __('Sales Shipping'),
                'type' => 'currency',
                'total' => 'sum',
                'currency_code' => $currencyCode,
                'index' => 'shipping_amount',
                'sortable' => false,
            ]
        );
        
        $this->addExportType('*/*/exportSalesCsv', __('CSV'));
        $this->addExportType('*/*/exportSalesExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }
}
