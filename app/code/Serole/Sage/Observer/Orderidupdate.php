<?php
namespace Serole\Sage\Observer;

class Orderidupdate implements \Magento\Framework\Event\ObserverInterface
{
  
  protected $_inventory;
  
  protected $_logger;
  
  
  public function __construct(
		\Serole\Sage\Model\Inventory $inventory
		) 
	{
		$this->_inventory = $inventory;
		$this->createLog('sage_Inventory_orderIDUpdate.log'); 
    }
  
  public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$this->_logger->info('orderIDUpdate Observer function start');
		
		$quoteId = $observer->getEvent()->getOrder()->getQuoteId();
       	$incrementId = $observer->getEvent()->getOrder()->getIncrementId();
		
		$this->_inventory->orderIDUpdate($quoteId, $incrementId);
		
		$this->_logger->info('orderIDUpdate Observer function End');
	}
  
  public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}

}