<?php
namespace Serole\Cashback\Observer;

class Cashback implements \Magento\Framework\Event\ObserverInterface
{
  protected $_usedpointsFactory;
  
   public function __construct(
                   \Serole\Cashback\Model\UsedpointsFactory $UsedpointsFactory
				 ) {
                $this->_usedpointsFactory = $UsedpointsFactory;
    }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
     $order = $observer->getEvent()->getOrder();
	 $payment = $order->getPayment();
     $method = $payment->getMethodInstance();
     $methodCode = $method->getCode();
     if($methodCode == "neatcoins")
	 {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$model = $om->create('Serole\Cashback\Model\Customerorder');
		$customerSession = $om->create('Magento\Customer\Model\Session');
		$customer_id = $customerSession->getCustomer()->getId();
		
		$usedPoint = $this->_usedpointsFactory->create();
		$usedPoint->setCustomerId($customer_id);
		$usedPoint->setOrderId($order->getIncrementId());
		$usedPoint->setOrderTotal($order->getGrandTotal());
		$usedPoint->setUsedPoints($order->getGrandTotal()*10);
		$usedPoint->save();
		 
	 }
     return $this;
  }
}