<?php

namespace Serole\Bulkemails\Controller\Adminhtml\Email;

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
           return $this->_authorization->isAllowed('Serole_Productattach::save');
       }

       public function _initAction()
       {
           $resultPage = $this->resultPageFactory->create();
           $resultPage->setActiveMenu(
               'Serole_Bulkemails::email_manage'
           )->addBreadcrumb(
               __('Bulk Emails'),
               __('Bulk Emails')
           )->addBreadcrumb(
               __('Bulk Emails CSV Upload'),
               __('Bulk Emails CSV Upload')
           );
           return $resultPage;
       }

      
       public function execute()
       {
           
           $resultPage = $this->_initAction();
           $resultPage->getConfig()->getTitle()->prepend(__('Bulk Emails'));
           $resultPage->getConfig()->getTitle()->prepend(__('Bulk Emails CSV Upload'));
           return $resultPage;
       }
   }