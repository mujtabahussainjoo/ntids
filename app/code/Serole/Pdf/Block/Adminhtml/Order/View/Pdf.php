<?php

   namespace Serole\Pdf\Block\Adminhtml\Order\View;

   class Pdf extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder {

       public function getOrder()
       {
           return $this->_coreRegistry->registry('current_order');
       }

       public function getSendPdfUrl($order)
       {
           // your custom url path here
           return $this->getUrl('pdfattachment/pdf/send', ['orderid' => $order->getId(),'incrementId' => $order->getRealOrderId()]);
       }

       public function getCreatePdfUrl($order)
       {
           // your custom url path here
           return $this->getUrl('pdfattachment/pdf/create', ['orderid' => $order->getId(),'email' => FALSE]);
       }

       public function getCreatePdfSendUrl($order)
       {
           // your custom url path here
           return $this->getUrl('pdfattachment/pdf/create', ['orderid' => $order->getId(),'email' => TRUE]);
       }

   }