<?php

namespace Serole\Partnerreport\Controller\Adminhtml\Salesorder;

use Magento\Backend\App\Action;


class Index extends \Magento\Backend\App\Action{


    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->backSession = $context->getSession();
        parent::__construct($context);
    }

    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_Partnerreport::partnerreport');
    }

    public function _initAction(){
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Serole_Partnerreport::partnerreport'
        )->addBreadcrumb(
            __('Sales Extract Report'),
            __('Sales Extract Report')
        )->addBreadcrumb(
            __('Sales Extract Report'),
            __('Sales Extract Report')
        );
        return $resultPage;
    }

    public function execute(){
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Sales Extract Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Sales Extract Report'));
        return $resultPage;
    }
}