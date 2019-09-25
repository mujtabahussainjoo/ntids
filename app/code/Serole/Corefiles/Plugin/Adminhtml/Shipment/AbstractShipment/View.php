<?php

   namespace Serole\Corefiles\Plugin\Adminhtml\Shipment\AbstractShipment;

   class View {

       public function afterExecute(){
           $resultForward = $this->resultForwardFactory->create();
           if ($this->getRequest()->getParam('shipment_id')) {
               $resultForward->setController('order_shipment')
                   ->setModule('admin')
                   ->setParams(['come_from' => 'shipment'])
                   ->forward('view');
               return $resultForward;
           } else {
               return $resultForward->forward('noroute');
           }
       }
   }