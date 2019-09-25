<?php

    namespace Serole\Orderreport\Cron;

    class NightlyMaintenance{

        protected $helperData;

        protected $objectManager;

        public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
                                    \Serole\HelpData\Helper\Data $helperData){
            $this->helperData = $helperData;
            $this->objectManager = $objectmanager;
        }

        public function execute(){
            $this->updatePrices();
        }

        public function updatePrices(){
           try {
               $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron-jon-nightly-ride.log');
               $logger = new \Zend\Log\Logger();
               $logger->addWriter($writer);

               $priceAttributeId = $this->helperData->getAttributeId('catalog_product','price');
               $specialProceAttributeId = $this->helperData->getAttributeId('catalog_product','special_price');

               $subsidyAttributeId = $this->helperData->getAttributeId('catalog_product','subsidy');
               $subsidyVipAttributeId = $this->helperData->getAttributeId('catalog_product','subsidy_vip');

               //$price_attribs = "68,67";
               //$subsidy_attribs = "208,252";
               $price_attribs = $specialProceAttributeId.','.$priceAttributeId;
               $subsidy_attribs = $subsidyAttributeId.','.$subsidyVipAttributeId;

               //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
               $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
               $connection = $resource->getConnection();
               $sql = "SELECT max(created_at) as last_update FROM price_history";
               $recs = $connection->query($sql);
               $lastUpdate = '1979-01-01';
               if ($recs && $row = $recs->fetch()) {
                   $lastUpdate = $row['last_update'];
               }
               $lastUpdate = '2015-10-20';
               $logger->info('Price History last update on '.$lastUpdate);

               $sql = "SELECT sku, updated_at FROM catalog_product_entity
				       WHERE updated_at > '".$lastUpdate."'";
               $prod_recs = $connection->query($sql);

                while ($prod_row = $prod_recs->fetch()){
                    $sku = $prod_row['sku']; //exit;
                   if ($sku != ''){
                       $logger->info('Processing '.$sku.'. product updated on '.$prod_row['updated_at']);
                       $stores = [];
                       $reason = [];
                       // We need to add the store to the array to be processed for this product
                       // if ANY of the following are true:
                       //  - There is a "Price" (aka RRP) for this product/store
                       //  - There is a "Special Price" for this product/store
                       //  - There is a "Subsidy" for this product/store
                       //  - There is a "VIP Subsidy" for this product/store
                       //  - There is a "VIP Price" for this product/store

                       //$prod_id = $objectManager->create('Magento\Catalog\Product\Model')->getIdBySku( $sku );
                       $prod_id = $this->helperData->getProductIdBySku($sku); //exit;

                       $sql = "SELECT store_id 
						       FROM catalog_product_entity_varchar subsidy 
						       WHERE subsidy.entity_id = ".$prod_id."
						       AND subsidy.attribute_id in (".$subsidy_attribs.")";
                       $recs = $connection->query($sql);
                       while ($row = $recs->fetch()){
                           $store_id = $row['store_id'];
                           if (!in_array($store_id, $stores)){
                               $stores[] = $store_id;
                               $reason[$store_id]='subsidy';
                           }
                       }

                       $sql = "SELECT store_id 
						       FROM catalog_product_entity_decimal price 
						       WHERE price.entity_id = ".$prod_id."
						       AND price.attribute_id in (".$price_attribs.")";
                       $recs = $connection->query($sql);
                       while ($row = $recs->fetch()){
                           $store_id = $row['store_id'];
                           if (!in_array($store_id, $stores)){
                               $stores[] = $store_id;
                               $reason[$store_id]='price';
                           }
                       }

                       /*
                        SELECT store.store_id
					           FROM catalog_product_entity_tier_price grp
					           JOIN store store
						       ON store.store_id = '".$store_id."'
                       AND store.website_id = grp.website_id
					           WHERE grp.entity_id=".$prod_id."
                       AND grp.qty = 1
                       AND grp.customer_group_id = 4 LIMIT 1
                       */

                       $sql = "SELECT store.store_id
					           FROM catalog_product_entity_tier_price grp
					           JOIN store store 
						       ON store.store_id = '".$store_id."'
						       AND store.website_id = grp.website_id
					           WHERE grp.entity_id=".$prod_id."					           
					           AND grp.customer_group_id = 4 LIMIT 1";

                      $recs = $connection->query($sql);

                       while ($row = $recs->fetch()){
                           $store_id = $row['store_id'];
                           if (!in_array($store_id, $stores)){
                               $stores[] = $store_id;
                               $reason[$store_id]='VIP price';
                           }
                       }

                       foreach($stores as $store_id){
                           $this->processProduct($store_id, $sku,$prod_row['updated_at'],$subsidyAttributeId,$subsidyVipAttributeId);
                       }
                   }
               }



           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
        }

        protected function processProduct($store_id, $sku, $updated_at,$subsidyAttributeId,$subsidyVipAttributeId){

            //$prod_id = Mage::getModel("catalog/product")->getIdBySku( $sku );

            $prod_id = $this->helperData->getProductIdBySku($sku);
            $prod  = $this->objectManager->create('Magento\Catalog\Model\Product')->setStore($store_id)->load($prod_id);
            $rrp = $prod->getPrice();

            $sell_price = $prod->getSpecialPrice();
            if ($sell_price == 0){
                $sell_price = $rrp;
            }
            $subsidy = $this->getSubsidy($store_id, $prod_id, $subsidyAttributeId);

            $vip_subsidy = $this->getSubsidy($store_id, $prod_id, $subsidyVipAttributeId);

            $vip_price = $this->getVipPrice($store_id, $prod_id);

            $this->writeRecord($store_id,$sku,$sell_price,$rrp,$subsidy,$vip_subsidy,$vip_price,$updated_at);
                             //$store_id, $sku, $sell_price, $rrp, $subsidy, $vip_subsidy, $vip_price, $updated_at
        }

        protected function getVipPrice($store_id, $prod_id){

            $sql = "SELECT value 
				    FROM catalog_product_entity_tier_price grp
				    JOIN store store 
					ON store.store_id = '".$store_id."'
					AND store.website_id = grp.website_id
				   WHERE grp.entity_id=".$prod_id."
				   AND grp.customer_group_id = 4
				   LIMIT 1";
            //$conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $recs = $connection->query($sql);
            $row = $recs->fetch();
            if ($row){
                return $row['value'];
            } else {
                return 0;
            }
        }

        protected function getSubsidy($store_id, $prod_id, $subsidy_attrib){
            $table = 'catalog_product_entity_varchar';

            $sql = "SELECT value 
				    FROM ".$table." subsidy 
				    WHERE subsidy.store_id = ".$store_id."
				    AND subsidy.entity_id = ".$prod_id."
				    AND subsidy.attribute_id = ".$subsidy_attrib;

            $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $recs = $connection->query($sql);
            $row = $recs->fetch();
            if ($row){
                return $row['value'];
            } else {
                return 0;
            }

        }

        protected function writeRecord($store_id, $sku, $sell_price, $rrp, $subsidy, $vip_subsidy, $vip_price, $updated_at){

             $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron-jon-nightly-ride-writeRecord.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $sql = "SELECT * FROM price_history 
				    WHERE store_id=".$store_id."
				    AND sku='".$sku."' 
				    ORDER BY created_at DESC
				    LIMIT 1";
            //$conn = Mage::getSingleton('core/resource')->getConnection('core_read');
            $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $recs = $connection->query($sql);
            $row = $recs->fetch();
            $writeRecord = true;

            // Only write the record if the values have changed.
            if ($row){
                /*
                echo "COMPARISON<br/>";
                echo "Sell Price: ".$row['sell_price']." == ". $sell_price."<br />";
                echo "rrp: ".$row['rrp']." == ". $rrp."<br />";
                echo "Subsidy: ".$row['subsidy']." == ". $subsidy."<br />";
                echo "VIP Subsidy: ".$row['vip_subsidy']." == ". $vip_subsidy."<br />";
                echo "VIP Price: ".$row['vip_price']." == ". $vip_price."<br />";
                */

                if ($row['sell_price']		== $sell_price
                    && $row['rrp']			== number_format($rrp,2)
                    && $row['subsidy']		== number_format($subsidy,2)
                    && $row['vip_subsidy']	== number_format($vip_subsidy,2)
                    && $row['vip_price']	== number_format($vip_price,2)){
                    $writeRecord = false;
                }

                $logger->info('Writing Price History for Store:'.$store_id.' SKU:'.$sku);
                $logger->info('  Sell Price: Old='	.$row['sell_price']	.' New='.$sell_price);
                $logger->info('  RRP: Old='			.$row['rrp']		.' New='.$rrp);
                $logger->info('  Subsidy: Old='		.$row['subsidy']	.' New='.$subsidy);
                $logger->info('  VIP Subsidy: Old='	.$row['vip_subsidy'].' New='.$vip_subsidy);
                $logger->info('  VIP Price: Old='	.$row['vip_price']	.' New='.$vip_price);
            }


            if ($writeRecord){
                //$conn = Mage::getSingleton('core/resource')->getConnection('core_write');
                $sql = "INSERT INTO price_history 
					(	store_id, 
						sku, 
						sell_price, 
						rrp, 
						subsidy, 
						vip_subsidy, 
						vip_price
						)
					VALUES (".$store_id.",
							'".$sku."',
							'".$sell_price."',
							'".$rrp."',
							'".$subsidy."',
							'".$vip_subsidy."',
							'".$vip_price."'
							)";
                $connection->query($sql);

            }

        }
    }