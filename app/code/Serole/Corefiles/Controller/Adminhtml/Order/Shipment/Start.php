<?php
  namespace Serole\Corefiles\Controller\Adminhtml\Order\Shipment;

  class Start extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Start  {


    public function execute(){
      $params = $this->getRequest()->getParams();
      $orderData = $this->getOrderData($params['order_id']);
      //echo "<pre>"; print_r($orderData); exit;
      $orderItems = $this->getOrderItems($params['order_id']);
      $serialCodes = $this->getSerialCodeData($params['order_id'],$orderData['increment_id']);
      $productQryForSerialCodes = 0;
      $serialCodesCount = count($serialCodes);
      $bundelArray = array();

      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      $productObj = $objectManager->create('Magento\Catalog\Model\Product');

      foreach ($orderItems as $key => $orderItem) {
        if(!$orderItem['parent_item_id']){
          $productData = $productObj->setStoreId($orderItem['store_id'])->load($orderItem['product_id']);
          if($productData->getIsserializeditem()){
              if($orderItem['product_type'] == 'simple' && $orderItem['product_type'] == 'virtual'){
                  $productQryForSerialCodes += $orderItem['qty_ordered'];
              }else{
                  if($orderItem['product_type'] == 'bundle') {
                      $bundelArray[$key] = $orderItem['qty_ordered'];
                  }else if($orderItem['product_type'] == 'grouped'){
                      /*$jsonDecode = $objectManager->create('\Magento\Framework\Serialize\SerializerInterface')->unserialize($orderItem['product_options']);
                      $gropedParentId = $jsonDecode['info_buyRequest']['super_product_config']['product_id']; //exit;
                      $productGroupedData = $productObj->setStoreId($orderItem['store_id'])->load($gropedParentId);
                      if($productGroupedData->getIsserializeditem()){*/
                          $productQryForSerialCodes += $orderItem['qty_ordered'];
                      //}
                  }
              }
          }
        }else{
            $parentId = $orderItem['parent_item_id'];
             if(isset($bundelArray[$parentId])){
                 $productQryForSerialCodes  += ($orderItem['qty_ordered'] * ($bundelArray[$parentId]));
             }
        }
      }
      if(((int)$productQryForSerialCodes == (int)$serialCodesCount) && $orderData['is_m1_order'] == 0){
           $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
      }else{
         $this->messageManager->addError("You can't create shipment for this order because serial-codes count not mathced with products count");
         $this->_redirect($this->_redirect->getRefererUrl());
      }
    }

    public function getOrderData($orderId){
       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $orderObj = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);
       return $orderObj->getData();
    }

    public function getOrderItems($orderId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceConnection = $objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resourceConnection->getConnection();
        $sql = "select * from sales_order_item where order_id = ".$orderId;
        $results = $connection->fetchAll($sql);
        return $results;
    }

    public function getSerialCodeData($orderId,$incrementId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceConnection = $objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resourceConnection->getConnection();
        $sql = "SELECT * FROM  `order_item_serialcode` WHERE  `OrderID` = ".$incrementId ." AND  `status` =1";
        $serialcodes = $connection->fetchAll($sql);
        return $serialcodes;
    }
  }
?>
