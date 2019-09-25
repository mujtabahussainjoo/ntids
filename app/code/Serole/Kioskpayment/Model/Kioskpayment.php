<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\Kioskpayment\Model;



/**
 * Pay In Store payment method model
 */
class Kioskpayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'kioskpayment';
	
	
    protected $_isGateway               = true;
 
    /**
     * can this method authorise?
     */
    protected $_canAuthorize            = true;
 
    /**
     * can this method capture funds?
     */
    protected $_canCapture              = true;
 
    /**
     * can we capture only partial amounts?
     */
    protected $_canCapturePartial       = false;
 
    /**
     * can this method refund?
     */
    protected $_canRefund               = false;
 
    /**
     * can this method void transactions?
     */
    protected $_canVoid                 = true;
 
    /**
     * can admins use this payment method?
     */
    protected $_canUseInternal          = true;
 
    /**
     * show this method on the checkout page
     */
    protected $_canUseCheckout          = true;
	
	 


    /*public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null) {

    }*/

    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/kiosk-payment-capture.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
		
        $orderId = $payment->getOrder()->getRealOrderId();

        $logger->info("Order-Id".$orderId);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
        $order = $payment->getOrder();
		
		$inventory = $objectManager->create('Serole\Sage\Model\Inventory');
		
		$helper = $objectManager->create('Serole\Sage\Helper\Data');
		
		$orderItems = $order->getAllItems();
		$quoteId = $order->getQuoteId();
		$logger->info("Quote".$quoteId);
		$i = 0;
		foreach($orderItems as $item)
			{
				$itemSku = $item->getSku();
				$itemQty = $item->getQtyOrdered();
				$logger->info("Sku:".$itemSku." Qty:".$itemQty);
				$_stockUpdateArray[] = "$quoteId,$itemSku,$itemQty,1";
				$_sku[] = $itemSku;
				$_skuQty[$i][trim($itemSku)]['qty'] = $item->getQty();
				$_skuQty[$i][trim($itemSku)]['type'] = "not-bundle";
				$_skuQty[$i][trim($itemSku)]['bundle-sku'] = "NA";
				 
			$i++;
			}
			
			$result = $inventory->getCheckStock($_sku, $_skuQty);
			if($result['error'] == 1)
				 {
					$message = $result["errorString"];
					$logger->info("Error in stock".$message);
                    throw new \Magento\Framework\Exception\LocalizedException(__($message));
				 }
				 else
				 {
					 if(!empty($_stockUpdateArray))
					 {
						 $updateResult = $inventory->stockUpdate($_stockUpdateArray);
						 
						 if($updateResult['error'] == 1)
						 {
							$logger->info("Error in block sc");
							throw new \Magento\Framework\Exception\LocalizedException(__('Unable to block Serial codes.'));
						 }
					 }
				 }
      
        if(!$order->canInvoice()){
            $logger->info("Can not Invoices".$orderId);
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action is not available.'));
        }

        $invoice = $objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order);

        if (!$invoice->getTotalQty()) {
            $logger->info("No Total Qty for".$orderId);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }

        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();
		$order->save();

        try {
            $invoiceCreate = $objectManager->create('Magento\Framework\DB\Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            $invoiceCreate->save();
			$order->addStatusHistoryComment(
                __('Completed processing using Kiosk Payment Method')
            )
            ->setIsCustomerNotified(false)
            ->save();
        }catch (\Exception $e){
            $logger->info("Expection for".$orderId."---".$e->getMessage());
        }

        $logger->info("Order Status of".$orderId."---".$order->getStatus());

        return true; 
    }
  

}
