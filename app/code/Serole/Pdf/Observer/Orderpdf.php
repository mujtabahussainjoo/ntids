<?php

namespace Serole\Pdf\Observer;

use Magento\Framework\Event\ObserverInterface;


class Orderpdf implements ObserverInterface {

    public $createPdf;

    public $storeManager;

    public function __construct(\Serole\Pdf\Model\Createpdf $createPdf,
                                \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->createPdf = $createPdf;
        $this->storeManager = $storeManager; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-pdfconcept-process.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        //$order = $observer->getEvent()->getOrder();
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        /*Getting data from order*/
        //$orderId = $order->getId();
        $orderId = $order->getIncrementId();
        //$logger->info("Order Id is".$orderId);
        $storeCode = $this->storeManager->getStore()->getCode();

        if($orderId) {
            try {
               /* if($storeCode == 'racvportal'){
                   $pdf = $this->createPdf->createPdfConcept($orderId, $email = FALSE,'frontend');
                }else{*/
                   $pdf = $this->createPdf->createPdfConcept($orderId, $email = FALSE,'frontend');
                //}
            } catch (\Exception $e) {
                $logger->info($e->getMessage());
            }
        }else{
            $logger->info("Order Id is emprty");
        }
    }

}

