<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\VisitHistory;

class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_registryManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_registryManager = $coreRegistry;
        parent::__construct($context);
    }

    public function execute()
    {
        $logId = $this->getRequest()->getParam('id');
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\VisitHistory')->load($logId);
        $this->_registryManager->register('amaudit_visithistory', $log, true);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_AdminActionsLog::amaudit');
        $pageResult->addBreadcrumb(__('Admin Actions Log'), __('Visit History'));
        $pageResult->addContent($pageResult->getLayout()->createBlock('Amasty\AdminActionsLog\Block\Adminhtml\VisitHistory\Edit'));
        $pageResult->getConfig()->getTitle()->prepend(__('Visit History '));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::page_visit_history');
    }
}
