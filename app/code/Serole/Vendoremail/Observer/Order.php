<?php

  namespace Serole\Vendoremail\Observer;

  use Magento\Framework\Event\Observer;
  use Magento\Framework\Event\ObserverInterface;

  class Order implements ObserverInterface {

      protected $product;

      public function __construct(\Magento\Catalog\Model\Product  $product){
          $this->product = $product;
      }

      public function  execute(Observer $observer){
          $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/vendoremail-observer-order-placeafter.log');
          $logger = new \Zend\Log\Logger();
          $logger->addWriter($writer);
          try {
              $order = $observer->getEvent()->getOrder();
              $orderAllItems = $order->getAllItems();
              $query = '';
              $queryShouldRun = 0;
              $incrementId = $order->getIncrementId();
				      foreach ($orderAllItems as $orderItem) {
                  $productId = $orderItem->getProductId();
                  $productObj = $this->product->load($productId);
                  $productVendorEmailEnable = $productObj->getData('enable_vendor_email');
                  $productVendorEmail = $productObj->getData('vendor_email_address');
                    if($productVendorEmailEnable == 1 && $productVendorEmail != ''){
                      $queryShouldRun = 1;
					            $mysqltime = date ("Y-m-d H:i:s");
					             //exit();
                      $query .= "('$incrementId','$productVendorEmail','$mysqltime','pending'),";
					          }
				      }
              if ($queryShouldRun == 1) {
                  $queryClean = substr($query, 0, -1);
                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                  $connection = $resource->getConnection();
        				   // print_r($queryClean);
        				   $sql = "Insert Into vendor_email_status (order_id,vendor_email,updated_at,status) Values " . $queryClean . ";";
        				    // exit();
                  $connection->query($sql);

              }
          }catch (\Exception $e){
              $logger->info($e->getMessage());
          }
      }
  }