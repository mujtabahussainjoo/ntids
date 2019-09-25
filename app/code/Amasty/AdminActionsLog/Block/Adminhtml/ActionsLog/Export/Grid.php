<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Export;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);
        $this->_objectManager = $objectManager;
    }

    protected function _prepareCollection()
    {
        $this->setDefaultSort('date_time');
        $this->setDefaultDir('desc');

        /**
         * @var \Amasty\AdminActionsLog\Model\Log $log
         */
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
        $collection = $log->getCollection();
        $collection->getSelect()
            ->joinLeft(
                array('u' => $this->_objectManager->get('Magento\User\Model\User')->getCollection()->getMainTable()),
                'main_table.username = u.username',
                array('fullname' => "CONCAT(firstname, ' ' ,lastname)", 'firstname', 'lastname')
            )
        ;
        $collection->addFilterToMap('username', 'main_table.username');
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }

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
            ]
        );


        return parent::_prepareColumns();
    }
}
