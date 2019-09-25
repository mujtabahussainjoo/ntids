<?php

namespace Serole\Cashback\Block\Customer;


class Dashboard extends \Magento\Framework\View\Element\Template {
	
	protected $_customerorderFactory;
	
	protected $_productRepositoryFactory;
	
	protected $_resource;

    public function __construct(
	               \Magento\Catalog\Block\Product\Context $context,
                   \Serole\Cashback\Model\CustomerorderFactory $CustomerorderFactory,
				   \Magento\Framework\App\ResourceConnection $resource,
                   \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,			   
				   array $data = []
				 ) {
                $this->_customerorderFactory = $CustomerorderFactory;
				$this->_resource = $resource;
				$this->_productRepositoryFactory = $productRepositoryFactory;
                parent::__construct($context, $data);

    }
	
	
	public function getAvilableCash()
	{
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
			$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		    $total = $connection->fetchOne("SELECT sum(rewards_points) as total FROM customer_order_detail where customer_id='$customer_id'");
			print_r($total);
	}
	
	public function getTotalPoints()
	{
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
			$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		    $total = $connection->fetchOne("SELECT sum(rewards_points) as total FROM customer_order_detail where customer_id='$customer_id'");
			return $total;
	}
	
	public function getTotalCash()
	{
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
			$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		    $total = $connection->fetchOne("SELECT sum(rewards_points) as total FROM customer_order_detail where customer_id='$customer_id'");
			return $total/10;
	}
	
	public function getUsedPoints()
	{
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
			$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		    $total = $connection->fetchOne("SELECT sum(used_points) as total FROM customer_used_points where customer_id='$customer_id'");
			return $total;
	}
	
	public function getAllOrdersCount()
	{
		     
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
		    
		    $collection = $this->_customerorderFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id));
			
			return count($collection);
					 
	}
	
	
	
	public function getAllOrders($limit=0)
	{
		     
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
		    if($limit != 0)
			{
		            return $collection = $this->_customerorderFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id))
							 ->setOrder('created_at','DESC')
							 ->setPageSize($limit);
			}
			else
			{
				    return $collection = $this->_customerorderFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id));
			}
			
			
							 
	}
	
  public function getOrdersGraph()
	{
		     
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
		   
			$collection = $this->_customerorderFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id));
							 
			$grapgData = array();			 
			foreach($collection as $orderData)
			{
				$custOrderData = $orderData->getData();
				$mon = date("M", strtotime($custOrderData['created_at']));
				$m = date_parse($mon);
				if(isset($grapgData[$m['month']]['point']))
				{
				  $grapgData[$m['month']]['point'] = $grapgData[$m['month']]['point']+$custOrderData['rewards_points'];
				  $grapgData[$m['month']]['month'] = $mon;
				}
			    else
				{
					$grapgData[$m['month']]['point'] = $custOrderData['rewards_points'];
					$grapgData[$m['month']]['month'] = $mon;
				}
			}
			ksort($grapgData);
			$strArray = array();
			foreach($grapgData as $k=>$v)
			{
				$monthName = $v['month'];
				$p = $v['point'];
				$strArray[] = "['$monthName', $p]";
			}
			
			
			
			return implode(",",$strArray);
							 
	}
	
	public function compare_months($a, $b) {
				$monthA = date_parse($a);
				$monthB = date_parse($b);

				return $monthA["month"] - $monthB["month"];
			}
	
	public function getImgUrl($prodId)
	{
		$objectManager =\Magento\Framework\App\ObjectManager::getInstance();
		$helperImport = $objectManager->get('Infortis\Infortis\Helper\Image');
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($prodId);
		return $helperImport->getImageUrl($product, "category_page_list", 50, 50);
		//return $imageUrl;
	}

	

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}