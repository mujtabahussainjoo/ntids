<?php
namespace Serole\Sage\Observer;
use Magento\Catalog\Model\ProductFactory;
class Checkstock implements \Magento\Framework\Event\ObserverInterface
{
 
  protected $_inventory;
  
  protected $_messageManager;

  protected $_redirect;
  
  protected $_helper;
  
  protected $_controller;
  
  protected $_actionFlag;
  
  protected $_objectManager;
  
  protected $_productFactory;

  
  public function __construct(
		  \Magento\Framework\Message\ManagerInterface $messageManager,
		  ProductFactory $productFactory,
		  \Magento\Framework\App\Response\RedirectInterface $redirect,
		  \Serole\Sage\Helper\Data $helper,
		  \Magento\Framework\App\ActionFlag $actionFlag,
		  \Serole\Sage\Model\Inventory $inventory)
	{
		$this->_inventory = $inventory;
		$this->_messageManager = $messageManager;
		$this->_redirect = $redirect;
		$this->_actionFlag = $actionFlag;
		$this->_productFactory = $productFactory;
		$this->_helper = $helper;
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
	}
  public function execute(\Magento\Framework\Event\Observer $observer)
  {
	  
	 $this->_controller = $observer->getControllerAction();
	 
     $addedItemId = $observer->getRequest()->getParam('product');
	 
	 $qty = $observer->getRequest()->getParam('qty');
	 
	 if(!isset($qty) || $qty == '')
		 $qty = 1;
     
	 $items = array();
	 $items[0]['identifier'] = $addedItemId;
	 $items[0]['type'] = "id";
	 $items[0]['qty'] = $qty;
	 
	 $result = $this->_inventory->getSageStockCheck($items);
		 
	 if($result['error'] == 1)
	 {
		$message = "This product is out of stock";
		return $this->_goBack($result['errorString'], $observer);
	 }
	
     return $this;
  }
	
  protected function _goBack($message, $observer)
	{
		
	   $this->_messageManager->addError($message);
		
		//to stop further processing
		$this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
		
		$result = [];
		
		if(!$observer->getRequest()->isXmlHttpRequest())
		{
			$backUrl =  $this->_redirect->getRefererUrl();
			header("Location:$backUrl");
			exit;
		}
		else
		{
			return $this->_controller->getResponse()->representJson(
					$this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
			);
		}
	}
}