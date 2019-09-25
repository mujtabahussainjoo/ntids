<?php

   namespace Serole\Serialcode\Controller\Adminhtml\Codes;

   use Magento\Backend\App\Action;

   class Index extends \Magento\Backend\App\Action{

       /**
        * @var \Magento\Framework\View\Result\PageFactory
        */
       private $resultPageFactory;

       /**
        * @param \Magento\Backend\App\Action\Context $context
        * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
        */
       public function __construct(
           \Magento\Backend\App\Action\Context $context,
           \Magento\Framework\View\Result\PageFactory $resultPageFactory
       ) {
           parent::__construct($context);
           $this->resultPageFactory = $resultPageFactory;
       }

       public function execute(){
           $resultPage = $this->resultPageFactory->create();
           $resultPage->setActiveMenu('Serole_Serialcode::serialcode_list');
           $resultPage->getConfig()->getTitle()->prepend(__('Serial Codes List'));
           return $resultPage;
       }
   }