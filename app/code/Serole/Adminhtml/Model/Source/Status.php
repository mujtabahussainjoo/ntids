<?php

  namespace Serole\Adminhtml\Model\Source;

  class Status implements \Magento\Framework\Option\ArrayInterface{

      protected $storeCollection;

      public function __construct(\Magento\Store\Model\Store $storeCollection){
          $this->storeCollection = $storeCollection;
      }
      
      public function toOptionArray(){
          $storeCollections = $this->storeCollection->getCollection();
          foreach ($storeCollections->getData() as $storeItem){
              $this->_options[] = ['label' => $storeItem['name'],'value' => $storeItem['store_id']];
          }
         return $this->_options;
      }
  }