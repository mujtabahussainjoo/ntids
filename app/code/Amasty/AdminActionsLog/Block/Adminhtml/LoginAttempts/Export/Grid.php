<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\LoginAttempts\Export;

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

    public function _construct()
    {
        parent::_construct();
        $this->setId('loginAttemptsGrid');
    }

    protected function _prepareCollection()
    {
        $this->setDefaultSort('date_time');
        $this->setDefaultDir('desc');
        $collection = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LoginAttempts')
            ->getCollection()
        ;
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
            'name', [
                'header' => __('Full Name'),
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'ip', [
                'header' => __('IP Address'),
                'index' => 'ip',
            ]
        );

        $this->addColumn(
            'location', [
                'header' => __('Location'),
                'index' => 'location',
            ]
        );

        $this->addColumn(
            'user_agent', [
                'header' => __('User Agent'),
                'index' => 'user_agent',
            ]
        );


        $this->addColumn('status', array(
            'header' => __('Status'),
            'index' => 'status',
            'width' => '120',
            'align' => 'left',
            'type' => 'options',
            'options' => [
                \Amasty\AdminActionsLog\Model\LoginAttempts::UNSUCCESS => __('Failed'),
                \Amasty\AdminActionsLog\Model\LoginAttempts::SUCCESS => __('Success'),
                \Amasty\AdminActionsLog\Model\LoginAttempts::LOGOUT => __('Logout'),
            ],
        ));


        return parent::_prepareColumns();
    }
}
