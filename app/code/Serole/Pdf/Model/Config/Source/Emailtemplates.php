<?php

   namespace Serole\Pdf\Model\Config\Source;

   class Emailtemplates implements \Magento\Framework\Option\ArrayInterface
   {
       public function toOptionArray(){
           $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
           $emailTemplateCollection = $objectManager->create('\Magento\Email\Model\Template')->getCollection();
           $options = [];
           $options[] = ['value' => '', 'label' => "Please Select Any Template"];
           foreach ($emailTemplateCollection->getData() as $emailTemplateItem){
               $options[] = ['value' => $emailTemplateItem['template_id'], 'label' => $emailTemplateItem['template_subject']];
           }
           return $options;
       }
   }

