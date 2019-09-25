<?php

namespace Serole\Accountpayment\Observer;

use Magento\Framework\Event\ObserverInterface;

use Psr\Log\LoggerInterface;

class Orderplaceafter implements ObserverInterface
{
	protected $logger;
	
	protected $_invoiceService;

	public function __construct(
	      LoggerInterface$logger,
		  \Magento\Sales\Model\Service\InvoiceService $invoiceService
    ) {
		 $this->logger = $logger;
		 $this->_invoiceService = $invoiceService;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
{
       	$ord = $observer->getEvent()->getOrder();
		$paymentMethod = $ord->getPayment()->getMethod();
		try{
			if($paymentMethod == "accountpayment")
			{
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				
				$order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($ord->getIncrementId());
				
				$invoice = $objectManager->create('Magento\Sales\Model\Service\InvoiceService');
				 
				$invoiceObj = $invoice->prepareInvoice($order);
				
				$invoiceObj->setRequestedCaptureCase('offline');
				
				$invoiceObj->register();
				
				$invoiceObj->getOrder()->setIsInProcess(true);
				$invoiceObj->save();
		   
				$transactionSave = $objectManager->create(
					\Magento\Framework\DB\Transaction::class
				)->addObject(
					$invoiceObj
				)->addObject(
					$invoiceObj->getOrder()
				);
				$transactionSave->save();
				
				//$order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
				//$order->save();
				
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
			exit;
		}
			
			
       
    }
}