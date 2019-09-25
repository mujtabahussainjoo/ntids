<?php

namespace Serole\Orderreport\Controller\Adminhtml\Subsidy;

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
        return $this->_authorization->isAllowed('Serole_Orderreport::subsidyorderreport');
    }

    public function _initAction(){
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Serole_Orderreport::subsidyorderreport'
        )->addBreadcrumb(
            __('Subsidy Invoice'),
            __('Subsidy Invoice')
        )->addBreadcrumb(
            __('Subsidy Invoice'),
            __('Subsidy Invoice')
        );
        return $resultPage;
    }

    public function execute(){
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Subsidy Order Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Subsidy Invoice'));
        return $resultPage;
    }
}