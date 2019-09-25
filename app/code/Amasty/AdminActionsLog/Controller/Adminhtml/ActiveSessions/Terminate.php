<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActiveSessions;

class Terminate extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $sessionId = $this->getRequest()->getParam('session_id');
        /**
         * @var \Amasty\AdminActionsLog\Model\ActiveSessions $activeModel
         */
        $activeModel= $this->_objectManager->get('Amasty\AdminActionsLog\Model\ActiveSessions');
        $activeModel->removeOnlineAdmin($sessionId);
        $activeModel->destroySession($sessionId);
        $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::active_sessions');
    }
}
