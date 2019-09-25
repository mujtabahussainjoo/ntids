<?php

namespace Serole\MemberList\Cron;
 
class Memberlistcleanup
{
	protected $logger;
	
	protected $_logger;
	
	Protected $_helper;
	
	protected $_eavAttribute;
	
	protected $_customerRepositoryInterface;
	
	protected $_orderCollectionFactory;
 
	public function __construct(
	    \Serole\MemberList\Helper\Data $helper,
		\Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Psr\Log\LoggerInterface $loggerInterface
	) {
		$this->logger = $loggerInterface;
		$this->_helper = $helper;
		$this->_eavAttribute = $eavAttribute;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->_orderCollectionFactory = $orderCollectionFactory;
      }
	
 
	public function execute() {
		
     $this->createLog('memberlistCleanup.log');
	 $this->_logger->info('customer cleanup cron started');
     // select all store
      $stores = $this->_helper->getAllStoresData();
	
	  if(isset($stores) && !empty($stores)) {
		    $this->createLog('memberlistCleanup.log');
		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$conn = $resource->getConnection();
			
		    $membernoAttributeId = $this->_eavAttribute->getIdByCode('customer', 'memberno');
			
			$this->_logger->info('membernoAttributeId'.$membernoAttributeId);

			
			foreach($stores as $storeId=>$storeData) {
				
			$sq = "SELECT v.entity_id as customer_id, v.value as member_no
					  FROM customer_entity_varchar v
					  JOIN customer_entity e 
						ON e.entity_id = v.entity_id 
						AND e.website_id = ".$storeId."
					  WHERE attribute_id = ".$membernoAttributeId." 
					  AND e.store_id != 80
					  AND NOT EXISTS (
						SELECT * 
						FROM customer_memberlist_detail 
						WHERE store='".$storeData['code']."'
						AND member_number = v.value
					  )";
				  
			$this->_logger->info('Suspend Query:'.$sq);
											  
				$customerRecs = $conn->query($sq);
											  
				
				$suspendCustomers = 0;
				$suspendCust = array();
				while ($customerRec = $customerRecs->fetch() ) {
					
					$customer_id = $customerRec['customer_id'];
					
					$customerFactory = $objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');
                    $customer = $customerFactory->getById($customer_id);
					
					$firstname = str_replace("'", "''", $customer->getFirstname());
					$lastname = str_replace("'", "''", $customer->getLastname());
					
				    $isSuspended = $customer->getCustomAttribute('is_suspended')->getValue();
					
					if (($firstname =='Dhananjay' and $lastname=='Kumar')
						|| ($firstname =='Mahesh' and $lastname=='Prasad')
						) {
						
					} else if (!$isSuspended) {
				
						// check if the customer has active orders
						$orders = $this->_orderCollectionFactory->create()
						          ->addAttributeToSelect('*')
								  ->addFieldToFilter('customer_id',$customer_id)
								  ->addFieldToFilter('status','pending');
								  
						if ($orders || $orders->count() == 0) {
							$this->_logger->info('SUSPEND '.$storeData['code'].': '.$firstname.' '.$lastname.'('.$customerRec['member_no'].')');
							$suspendCust[] = $customer_id;
							$suspendCustomers++;
							//$customer->setCustomAttribute("is_suspended",1);
							//$customerFactory->save($customer);
						}
						
					}
				}
				if(!empty($suspendCust))
				{
					$suspendCustList = implode(",",$suspendCust);
					$suspendQuery = "update customer_entity set is_suspended='1' where entity_id in ($suspendCustList)";
					$conn->query($suspendQuery);
					$this->_logger->info("Store:".$storeData['code']." Suspend Customer Query Final:".$suspendQuery);
				}
				$this->_logger->info($suspendCustomers.' customers suspended');
				
				
				//For Unsuspending customer
				
				$usq = "SELECT v.entity_id as customer_id, v.value as member_no
											  FROM customer_entity_varchar v
											  JOIN customer_entity e 
												ON e.entity_id = v.entity_id 
												AND e.website_id = ".$storeId."
											  WHERE attribute_id = ".$membernoAttributeId." 
											  AND e.store_id != 80
											  AND EXISTS (
												SELECT * 
												FROM customer_memberlist_detail 
												WHERE store='".$storeData['code']."'
												AND member_number = v.value
											  )";
											  
				$this->_logger->info("UnSuspend Query:".$usq);
			
				$customerRecs =  $conn->query($usq);		
										
				$unsuspendCustomers = 0;
				$unsuspendCust = array();
				while ($customerRec = $customerRecs->fetch() ) {
					$customer_id = $customerRec['customer_id'];
					
					$customerFactory = $objectManager->create('\Magento\Customer\Api\CustomerRepositoryInterface');
                    $customer = $customerFactory->getById($customer_id);
					
					$firstname = str_replace("'", "''", $customer->getFirstname());
					$lastname = str_replace("'", "''", $customer->getLastname());
					
				    $isSuspended = $customer->getCustomAttribute('is_suspended')->getValue();
					
				 if ($isSuspended) {
					        $this->_logger->info('UNSUSPEND '.$storeData['code'].': '.$firstname.' '.$lastname.'('.$customerRec['member_no'].')');
							$unsuspendCust[] = $customer_id;
							//$customer->setCustomAttribute("is_suspended",0);
							//$customerFactory->save($customer);
							$unsuspendCustomers++;
					}
				}
				if(!empty($unsuspendCust))
				{
					$unsuspendCustList = implode(",",$unsuspendCust);
					$unsuspendQuery = "update customer_entity set is_suspended='0' where entity_id in ($unsuspendCustList)";
					$conn->query($unsuspendQuery); 
					$this->_logger->info("Store:".$storeData['code']." UNSuspend Customer Query Final:".$unsuspendQuery);
				}
				
				$this->_logger->info($unsuspendCustomers.' customers unsuspended');
			}
		} 
       $this->_logger->info('customer cleanup cron Ended');
	}
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
