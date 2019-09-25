<?php
namespace Serole\Subscriber\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class Subsriberdata implements \Magento\Framework\Event\ObserverInterface
{
	protected $_order;
	protected $date;
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
    ){
         $this->_order = $order; 
		 $this->date = $date;		 
    }

	public function execute(\Magento\Framework\Event\Observer $observer){
		$orderids = $observer->getEvent()->getOrderIds();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		$customerIdContrl=$customerSession->getCustomerIdController();
		$customerIdObsrv = $customerSession->getId();
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/AAASubscriberObserver.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		//echo "Observer===".$customerIdObsrv = $customerSession->getId();
		//echo "<br/>";
		//echo "Controller===".$customerIdContrl;

		if($customerIdContrl == $customerIdObsrv){ 
			//echo "if";
			//$billingAddress = $order->getBillingAddress();
			//$orderData =$observer->getEvent()->getOrder();
			//echo "<pre>";
				//print_r($billingAddress->getData());
			//	print_r($orderids);
			//echo "<br/>";
			$customerId=$customerIdObsrv;
			foreach($orderids as $orderid){
				$order = $this->_order->load($orderid);
				$firstname = $order->getBillingAddress()->getFirstname();
				//echo "<br/>";
				$lastname = $order->getBillingAddress()->getLastname();
				//echo "<br/>";
				$telephone = $order->getBillingAddress()->getTelephone();
				//echo "<br/>";
				$postcode = $order->getBillingAddress()->getPostcode();
				//echo "<br/>";
				$region = $order->getBillingAddress()->getRegion();
				//echo "<br/>";
				$city = $order->getBillingAddress()->getCity();
				//echo "<br/>";
				$billingAddress=$city.','.$region.','.$postcode;
				//echo "<br/>";
				$storeId =$order->getStoreId();
				//echo "<br/>";
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
				$tableName = $resource->getTableName('customer_entity_varchar');
				$sql = "Select value FROM $tableName Where entity_id=$customerIdContrl And attribute_id=169";
				$result = $connection->fetchAll($sql);
				if(isset($result[0]['value']) && isset($result[0]))
				    $customer_member=$result[0]['value'];
				else
					$customer_member='';
				//echo "<br/>";
				$mail=$order->getBillingAddress()->getEmail();
				//echo "<br/>";
				$currentTime = $this->date->gmtDate();
				$data = array('customer_id'=>$customerId,'customer_email'=>$mail,'customer_member'=>$customer_member,'customer_first_name'=>$firstname,'customer_last_name'=>$lastname,'customer_phno'=>$telephone,'customer_address'=>$billingAddress,'customer_postcode'=>$postcode,'customer_state'=>$region,'suburb'=>$city,'order_id'=>$orderid,'export'=>0,'opt_in'=>1,'store_id'=>$storeId,'created_at'=>$currentTime,'updted_at'=>$currentTime);
				$customData = $objectManager->create('Serole\Subscriber\Model\Subscriber')->setData($data);
				$customData->save();
				
			}
			$customerSession->unsCustomerIdController();
		}
    return $this;
  }
}