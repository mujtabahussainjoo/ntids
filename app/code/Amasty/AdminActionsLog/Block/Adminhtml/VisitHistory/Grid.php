<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\VisitHistory;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_resource;
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);

        $this->_objectManager = $objectManager;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('visitHistoryGrid');
    }

    protected function _prepareCollection()
    {
        $this->setDefaultSort('session_start');
        $this->setDefaultDir('desc');
        /**
         * @var \Amasty\AdminActionsLog\Model\VisitHistory $model
         */
        $model = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory');
        $collection = $model->getCollection();
        $this->setCollection($collection);

        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeSessionsModel
         */
        $activeSessionsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
        $activeSessionsModel->checkOnline();

        return parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
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
            'session_start', [
                'header' => __('Session Start'),
                'index' => 'session_start',
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'session_end', [
                'header' => __('Session End'),
                'index' => 'session_end',
                'type' => 'datetime'
            ]
        );

        $this->addColumn(
            'ip', [
                'header' => __('Ip Address'),
                'index' => 'ip',
            ]
        );

        $this->addColumn(
            'location', [
                'header' => __('Location'),
                'index' => 'location',
            ]
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit', ['id' => $row->getId()]
        );
    }
}
