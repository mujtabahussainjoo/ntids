<?php

   namespace Serole\GiftMessage\Observer;

   class GiftMessage implements \Magento\Framework\Event\ObserverInterface
   {

       protected $customerSession;

       protected $giftMessage;

       public function __construct(\Magento\Customer\Model\Session $customerSession,
                                   \Serole\GiftMessage\Model\Message $giftMessage ){
           $this->customerSession =  $customerSession;
           $this->giftMessage = $giftMessage;
       }

       public function execute(\Magento\Framework\Event\Observer $observer){
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/gift-message-observer.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);
           
           $order = $observer->getEvent()->getOrder();
           $orderId = $order->getId();
          // $isVirtual = $order->getIsVirtual();
           $incrementId = $order->getIncrementId();

           try {
               if($this->customerSession->getToName()) {
                   if ($this->customerSession->getToName()) {
                       $giftObj = $this->giftMessage;
                       $giftObj->setOrderId($incrementId);
                       $giftObj->setTo($this->customerSession->getToName());
                       $giftObj->setFrom($this->customerSession->getFromName());
                       $giftObj->setMessage($this->customerSession->getGiftMessage());
                       $giftObj->setEmail($this->customerSession->getGiftEmail());
                       $giftObj->setImage($this->customerSession->getGiftImage());
                       if ($giftObj->save()) {
                           $this->customerSession->unsToName();
                           $this->customerSession->unsFromName();
                           $this->customerSession->unsGiftMessage();
                           $this->customerSession->unsGiftEmail();
                           $this->customerSession->unsGiftImage();
                       }
                   }
               }
           }catch(\Exception $e){
              $logger->info($e->getMessage());
           }
       }
   }