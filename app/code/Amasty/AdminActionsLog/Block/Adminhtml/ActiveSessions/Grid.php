<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActiveSessions;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_objectManager;
    protected $_date;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        array $data = []
    )
    {
        parent::__construct($context, $backendHelper, $data);

        $this->_objectManager = $objectManager;
        $this->_date = $date;
    }

    public function _construct()
    {
        parent::_construct();
        $this->setId('activeSessionsGrid');
    }

    protected function _prepareCollection()
    {
        $this->setDefaultSort('date_time');
        $this->setDefaultDir('desc');
        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeSessionsModel
         */
        $activeSessionsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
        $collection = $activeSessionsModel->getCollection();
        $this->setCollection($collection);

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
            'date_time', [
                'header' => __('Logged In At'),
                'index' => 'date_time',
                'type' => 'datetime',
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
            'recent_activity', [
                'header' => __('Recent Activity'),
                'index' => 'recent_activity',
                'filter'   => false,
                'frame_callback' => [$this, 'decorateRecentActivity']
            ]
        );

        $link= $this->getUrl('amaudit/activesessions/terminate') .'session_id/$session_id';
        $this->addColumn('action', array(
            'header'   => __('Actions'),
            'sortable' => false,
            'filter'   => false,
            'type'     => 'action',
            'actions'  => array(
                array(
                    'url'     => $link,
                    'caption' => __('Terminate Session'),
                    'confirm' => __('Are you sure?'),
                ),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function decorateRecentActivity($currentTimeStamp)
    {
        $_minute = 60;
        $_hour = 3600;
        $_3hours = 10800;

        $currentTime = $this->_date->timestamp();
        $rowTime = strtotime($currentTimeStamp);
        $timeDifference = $currentTime - $rowTime;

        if ($timeDifference < $_minute) {
            return __('Just Now');
        } elseif ($timeDifference < $_hour) {
            $minutes = round($timeDifference / 60);
            return  __($minutes . " minute(s) ago");
        } elseif ($timeDifference < $_3hours) {
            $hours = round($timeDifference / 3600);
            return __($hours . " hour(s) ago");
        }

        return $currentTimeStamp;
    }
}
