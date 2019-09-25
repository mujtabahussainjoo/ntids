<?php

namespace Serole\SubsidyOrderattributes\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Order implements ObserverInterface {
    protected $product;
    protected $customerSession;
    protected $orderItem;
    public function __construct(\Magento\Catalog\Model\Product $product,\Magento\Sales\Model\Order\Item $orderItem,\Magento\Customer\Model\Session $customerSession){
        $this->product = $product;
        $this->customerSession = $customerSession;
        $this->orderItem = $orderItem;
    }

    public function  execute(Observer $observer){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subsidy-observer-order-placeafter.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('sales_order_item'); //gives table name with prefix
        try {
            $order = $observer->getEvent()->getOrder();
            $orderAllItems = $order->getAllItems();
            $incrementId = $order->getIncrementId();
            $customerGroupId = $this->customerSession->getCustomer()->getGroupId();
            foreach ($orderAllItems as $orderItem) {
                $orderItemId = $orderItem->getId();
                $productId = $orderItem->getProductId();
                $productObj = $this->product->load($productId);
				
                if($customerGroupId === 4) {
                    if($productObj->getSubsidyVip()){
                       // $orderItem = $this->orderItem->load($orderItem->getId());
                        $orderItem->setSubsidyVip($productObj->getSubsidyVip());
                    }
                }else{
                    if($productObj->getSubsidy()){
                        //$orderItem = $this->orderItem->load($orderItemId);
						$logger->info($orderItem->getId());
						$Subsidy=$productObj->getSubsidy();
						$orderItem->setSubsidy($Subsidy);
						$logger->info($Subsidy);
                    }
                }
                if($productObj->getMemberProfit()){
                    //$orderItem = $this->orderItem->load($orderItem->getId());
                    $orderItem->setMemberProfit($productObj->getMemberProfit());
                }
				
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }
}