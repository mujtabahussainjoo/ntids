<?php

   namespace Serole\GiftMessage\Block;

   class Order extends \Magento\Framework\View\Element\Template
   {

       protected $registry;

       protected $giftImage;

       protected $pdfHelper;

       protected $giftMessage;


       public function __construct(
           \Magento\Framework\View\Element\Template\Context $context,
           \Magento\Framework\Registry $registry,
           \Serole\GiftMessage\Model\Image $giftImage,
           \Serole\GiftMessage\Model\Message $giftMessage,
           \Serole\Pdf\Helper\Pdf $pdfHelper,
           array $data = []
       ) {
           $this->registry = $registry;
           $this->giftImage = $giftImage;
           $this->giftMessage = $giftMessage;
           $this->pdfHelper = $pdfHelper;
           parent::__construct($context, $data);
       }


       public function getOrder(){
           return $this->registry->registry('current_order');
       }

       public function getGiftMessage($incrementId){
           $giftMessageData = $this->giftMessage->getCollection();
           $giftMessageData->addFieldToFilter('order_id',$incrementId);
           return $giftMessageData->getFirstitem()->getData();
       }

       public function getGiftImage($image){
           $imageData = $this->giftImage->getCollection();
           $imageData->addFieldToFilter('id',$image);
           return $imageData->getFirstItem()->getData();

       }

       public function getImagepath($folderName,$imageName){
           $filePath = $this->pdfHelper->getFilePath($folderName,$imageName);
           return $filePath;
       }

       public function getImageUrl($folderName,$imageName){
           $fileUrl = $this->pdfHelper->getFileUrl($folderName,$imageName);
           return $fileUrl;
       }


   }