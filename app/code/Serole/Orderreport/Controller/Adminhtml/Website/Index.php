<?php

namespace Serole\Orderreport\Controller\Adminhtml\Website;

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
        return $this->_authorization->isAllowed('Serole_Orderreport::websiteorderreport');
    }

    public function _initAction(){
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Serole_Orderreport::websiteorderreport'
        )->addBreadcrumb(
            __('Website Orders'),
            __('Website Orders')
        )->addBreadcrumb(
            __('Website Orders'),
            __('Website Orders')
        );
        return $resultPage;
    }

    public function execute(){
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Website Order Report'));
        $resultPage->getConfig()->getTitle()->prepend(__('Website Orders'));
        return $resultPage;
    }
}