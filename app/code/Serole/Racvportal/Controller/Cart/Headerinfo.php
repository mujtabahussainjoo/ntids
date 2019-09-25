<?php

   namespace Serole\Racvportal\Controller\Cart;

   class Headerinfo extends \Serole\Racvportal\Controller\Cart\Ajax{

       public function execute(){
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-ajax-headerInfo.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           try {
               //if ($this->getRequest()->isAjax()) {
               $customerSession =  $this->customerSession;
               if ($customerSession->isLoggedIn()) {
                   $memberNo = $this->customerSession->getMemberNo();
                   $customerData = $this->customerSession->getCustomerData();
                   $customerName = $customerData->getFirstname().' '.$customerData->getLastname();
                   $shopNo = $this->customerSession->getRacvShop();
                   $shopObj = $this->shops->load($shopNo);
                   $shopName = $shopObj->getName();
                   //$data = array('memberno' => $memberNo, 'customername' => $customerName, 'shopname' => $shopName);
                   /*$resultPage = $this->resultPageFactory->create();
                   $block = $resultPage->getLayout()
                                       ->createBlock('Serole\Racvportal\Block\Onepage')
                                       ->setTemplate('Serole_Racvportal::headerinfo.phtml')
                                       ->setData('data',$data);
                   $htmlResponse = $block->toHtml();
                   $data['html'] = $htmlResponse;*/
                   $data['memberno'] = $memberNo;
                   $data['customername'] = $customerName;
                   $data['shopname'] = $shopName;
                   $data['status'] = 'sucess';
                   $data['customersession'] = 'yes';
                   echo json_encode($data);
               }
               //}
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }
   }