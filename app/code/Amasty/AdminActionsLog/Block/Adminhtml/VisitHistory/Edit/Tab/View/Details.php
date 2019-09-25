<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\VisitHistory\Edit\Tab\View;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;


class Details extends Generic implements TabInterface
{
    protected $_objectManager;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Visits History');
    }

    public function getTabTitle()
    {
        return __('Visits History');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getLogRows()
    {
        /**
         * @var \Amasty\AdminActionsLog\Model\VisitHistoryDetails $detailsModel
         */
        $detailsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistoryDetails');
        $collection = $detailsModel->getCollection();
        $registryLog = $this->_coreRegistry->registry('amaudit_visithistory');
        if (!$registryLog)
        {
            return [];
        }
        else
        {
            $logRows = [];

            $collection->addFieldToFilter('session_id', array('in' => $registryLog->getSessionId()));
            foreach ($collection as $row) {
                $logRow = $row->getData();
                $logRow['stay_duration'] = $this->_secondsToTime($logRow['stay_duration']);
                $logRows[] = $logRow;
            }

            return $logRows;
        }
    }

    protected function _secondsToTime($seconds)
    {
        $timeString = '';
        $minute = 60;
        $hour = 3600;

        $hours = floor($seconds / $hour);
        $minutes = floor(($seconds - $hour * $hours) / $minute);
        $seconds = $seconds - ($hours * $hour) - ($minutes * $minute);

        if ($hours > 0) {
            $hoursText = 'hours';
            if ($hours == 1) {
                $hoursText = 'hour';
            }
            $timeString = $timeString . ' ' . $hours . ' ' . __($hoursText);
        }

        if ($minutes > 0) {
            $minutesText = 'minutes';
            if ($minutes == 1) {
                $minutesText = 'minute';
            }
            $timeString = $timeString . ' ' . $minutes . ' ' . __($minutesText);
        }

        if ($seconds > 0) {
            $secondsText = 'seconds';
            if ($seconds == 1) {
                $secondsText = 'second';
            }
            $timeString = $timeString . ' ' . $seconds . ' ' . __($secondsText);
        }

        return $timeString;
    }
}
