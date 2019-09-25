<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View;

class VisitHistoryDetails extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\AdminActionsLog\Model\ResourceModel\VisitHistoryDetails');
    }

    protected $_date;
    protected $_contextView;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Model\Context $context,
        View\Element\Template\Context $contextView
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->_contextView = $contextView;
    }

    public function getLastSessionPage($sessionId)
    {
        $lastItem = NULL;
        $visitDetailsCollection = $this->getCollection();
        $visitDetailsCollection->getSelect()
            ->where("session_id = (?)", $sessionId);
        $lastItem = $visitDetailsCollection->getLastItem();

        return $lastItem;
    }

    public function saveLastPageDuration($sessionId)
    {
        $lastPage = $this->getLastSessionPage($sessionId);
        $lastPageData = $lastPage->getData();
        $time = time();

        $lastPageTime = $this->_contextView->getSession()->getLastPageTime();

        if (!empty($lastPageData) && $lastPageTime) {
            $duration = $time - $lastPageTime;
            $lastPage->setStayDuration($duration);
            $lastPage->save();
        }
    }
}
