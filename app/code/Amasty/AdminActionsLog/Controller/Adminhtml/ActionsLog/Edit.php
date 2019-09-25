<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

class Edit extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_helper;
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
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log')->load($logId);
        $this->_registryManager->register('amaudit_actionslog', $log, true);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $pageResult = $this->resultPageFactory->create();
        $pageResult->getLayout();
        $pageResult->setActiveMenu('Amasty_AdminActionsLog::amaudit');
        $pageResult->addBreadcrumb(__('Admin Actions Log'), __('Actions Log'));
        $pageResult->addContent($pageResult->getLayout()->createBlock('Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Edit'));
        $pageResult->getConfig()->getTitle()->prepend(__('Actions Log '));

        return $pageResult;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}
