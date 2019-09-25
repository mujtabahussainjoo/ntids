<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

abstract class DefaultItemColumns extends \Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs\DefaultLog
{
    protected function _prepareColumns()
    {
        $this->addColumn(
            'date_time', [
                'header' => __('Date'),
                'index' => 'date_time',
                'type' => 'datetime',
            ]
        );

        $this->addColumn(
            'username', [
                'header' => __('Username'),
                'index' => 'username',
            ]
        );

        $this->addColumn(
            'fullname', [
                'header' => __('Full Name'),
                'index'  => 'fullname',
                'filter_condition_callback' => [$this, '_filterFullnameCondition'],
            ]
        );

        $this->addColumn(
            'type', [
                'header' => __('Action Type'),
                'index' => 'type',
                'frame_callback' => array($this, 'decorateStatus'),
            ]
        );

        $this->addColumn(
            'category_name', [
                'header' => __('Object'),
                'index' => 'category_name',
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id', [
                    'header' => __('Store View'),
                    'index' => 'store_id',
                    'type' => 'store',
                    'store_all' => true,
                    'store_view' => true,
                    'skipEmptyStoresLabel' => 1,
                    'sortable' => true,
                ]
            );
        }

        $this->addColumn(
            'item', [
                'header' => __('Item'),
                'index' => 'item',
                'frame_callback' => [$this, 'showOpenElementUrl'],
                'renderer' => 'Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Item'
            ]
        );

        $this->addColumn(
            'action', [
                'header' => __('Actions'),
                'index' => 'action',
                'filter'         => false,
                'sortable'       => false,
                'frame_callback' => [$this, 'showActions'],
            ]
        );

        $this->addExportType('*/*/exportActionsLogCsv', __('CSV'));
        $this->addExportType('*/*/exportActionsLogExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    protected function _filterFullnameCondition($collection, $column)
    {
        $fullname = $column->getFilter()->getValue();
        $collection->getSelect()->where('CONCAT(firstname, \' \' ,lastname) like ?', '%' . $fullname . '%');
    }
}
