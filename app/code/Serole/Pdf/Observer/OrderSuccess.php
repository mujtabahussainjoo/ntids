<?php

   namespace Serole\Pdf\Observer;

  use Magento\Framework\Event\Observer;

  class OrderSuccess implements \Magento\Framework\Event\ObserverInterface{

      public $order;

      public $giftMessage;

      public $pdfHelper;
	  
	  protected $giftImage;

      public function __construct(\Serole\GiftMessage\Model\Message $giftMessage,
	                              \Serole\GiftMessage\Model\Image $giftImage,
                                  \Serole\Pdf\Helper\Pdf $pdfHelper,
                                  \Magento\Sales\Model\Order $order)
      {
          $this->order = $order;
          $this->giftMessage = $giftMessage;
		  $this->giftImage = $giftImage;
          $this->pdfHelper = $pdfHelper;
      }

      public function execute(Observer $observer){
          $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Order-success_controller.log');
          $logger = new \Zend\Log\Logger();
          $logger->addWriter($writer);

          $orderIds = $observer->getEvent()->getOrderIds();
          $orderId = $orderIds[0];

          $customerData = array();

          $order = $this->order->load($orderId);
          $incrementId = $order->getIncrementId();
          $giftMessageObj = $this->giftMessage->getCollection();
          $giftMessageObj->addFieldToFilter('order_id', $incrementId);
          $giftMessageData = $giftMessageObj->getFirstItem()->getData();

          if ($giftMessageData) {
              $emailTemplateObj = $this->giftImage->load($giftMessageData['image']);
              $emailTemplateData = $emailTemplateObj->getData();
              $customerData['toname'] = $giftMessageData['to'];
              $customerEmail = $giftMessageData['email'];
              $customerData['message'] = $giftMessageData['message'];
              $customerData['fromname'] = $giftMessageData['from'];
              if ($emailTemplateData) {
                  $customerData['emailtemplateid'] = $emailTemplateData['emailtemplateid'];
              }
          } else {
              $customerFirstName = $order->getCustomerFirstname();
              $customerLastName = $order->getCustomerLastname();
              $deliveryemail = $order->getDeliveryemail();
              if ($deliveryemail) {
                  $customerEmail = $deliveryemail;
              } else {
                  $customerEmail = $order->getCustomerEmail();
              }
              $customerData['toname'] = $customerFirstName . ' ' . $customerLastName;
          }
          $customerData['email'] = $customerEmail;
		  $customerData['customerName'] =$order->getCustomerFirstname();
          $rootDir = $this->pdfHelper->getRootBaseDir();
          $pdfFilesPath = $rootDir . 'neatideafiles/pdf/';
          $pdfFileName = $incrementId.".pdf";
          $filePath = $pdfFilesPath.$pdfFileName;
          if(file_exists($filePath)){
			  $customerData['customerName'] =$order->getCustomerFirstname();
			  $customerData['orderid'] =$order->getIncrementId();
			  $customerData['storename'] =$order->getstoreName();
              $mailStatus = $this->pdfHelper->sendPdfToCustomerEmail($filePath, $customerData, $incrementId);
          }
      }
  }