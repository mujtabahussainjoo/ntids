<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View;

class handleLayoutRenderBefore implements ObserverInterface
{
    protected $_objectManager;
    protected $_context;

    public function __construct(
        View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        $this->_context = $context;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $context = $this->_context;
        $session = $context->getSession();
        $sessionId = $session->getSessionId();
        $visitEntityData = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory')->getVisitEntity($sessionId)->getData();
        if (!empty($visitEntityData)) {
            $pageTitleBlock = $context->getLayout()->getBlock('page.title');
            if ($pageTitleBlock !== false) {
                $pageConfig = $context->getPageConfig();
                $title = $pageConfig->getTitle()->get();
                $pageUrl = $context->getUrlBuilder()->getCurrentUrl();

                $detailData['page_name'] = $title;
                $detailData['page_url'] = $pageUrl;
                $detailData['session_id'] = $sessionId;
                $detailData['visit_id'] = $visitEntityData['id'];

                /**
                 * @var \Amasty\AdminActionsLog\Model\VisitHistoryDetails $detailsModel
                 */
                $detailsModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistoryDetails');
                $detailsModel->saveLastPageDuration($sessionId);
                $session->setLastPageTime(time());
                $detailsModel->setData($detailData);
                $detailsModel->save();
            }
        }
    }
}
