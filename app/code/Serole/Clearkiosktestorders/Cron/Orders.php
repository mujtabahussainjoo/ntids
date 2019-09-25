<?php

namespace Serole\Clearkiosktestorders\Cron;


use Magento\Store\Model\Store;

class Orders
{

    protected $product;

    protected $brainTreeConfig;

    protected $helpData;

    protected $orderObj;

    protected $connection;

    protected $creditmemoFactory;

    public function __construct(\Magento\Catalog\Model\Product $product,
                                \Magento\Sales\Model\Order $orderObj,
                                \Serole\HelpData\Helper\Data $helpData,
                                \Magento\Store\Model\Store $store,
                                \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
                                \Magento\Framework\App\ResourceConnection $connection,
                                \Magento\Braintree\Gateway\Config\Config $brainTreeConfig
    )
    {
        $this->product = $product;
        $this->brainTreeConfig = $brainTreeConfig;
        $this->helpData = $helpData;
        $this->orderObj = $orderObj;
        $this->store = $store;
        $this->connection = $connection;
        $this->creditmemoFactory = $creditmemoFactory;
    }

        public function cleartestorders(){

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Clearkiosktestorders-cron-cleartestorders.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            try {

                $storeId = $this->getStoreId('rackiosks_en');
                $orders = $this->orderObj->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('status', array('nin' => array('canceled', 'closed', 'complete')))
                    ->addFieldToFilter('store_id', $storeId);

                foreach ($orders as $order) {
                    $logger->info('Cancelling: ' . $order->getIncrementId());
                    $order->cancel();
                    $order->setState('Cancel Transaction.');
                    //$order->setState(\Magento\Sales\Model\Order::STATE_CANCELED,'Cancel Transaction.');
                    $order->setStatus("canceled");
                    $order->save();
                }

                $conn = $this->connection->getConnection();
                $pdfSQL = "update order_pdf_status set status = 'kiosk' where order_id like $storeId.'%' and status != 'kiosk' ";
                $logger->info('PDF SQL:' . $pdfSQL);
                $res = $conn->query($pdfSQL);

                /*
                 $serialSQL = "update serialcodes set note='', status = 0 WHERE note LIKE  $storeId.'%' AND DATE( update_time ) >  '2016-02-01' ";
                 $logger->info('Serial SQL:'.$serialSQL);
                 $res = $conn->query($serialSQL);
                */

                $logger->info('Finished');
            }catch (\Exception $e){
                $logger->info($e->getMessage());
            }
        }

       public function clearfailedorders(){
		   return;
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Clearkiosktestorders-cron-clearfailedorders.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);

           try {

               $storeId = $this->getStoreId('rackiosks_en');

               $order = $this->orderObj->getCollection()
                   ->addAttributeToSelect('entity_id')
                   ->addAttributeToSelect('created_at')
                   ->addAttributeToSelect('status')
                   ->addFieldToFilter('store_id', $storeId)
                   ->addFieldToFilter('created_at', array(
                       'from' => strtotime('-20 minutes', time()),
                       'to' => strtotime('-10 minutes', time()),
                       'datetime' => true
                   ))
                   ->setOrder('entity_id', 'desc')
                   ->load();
               $logger->info((string)$order->getSelect());

               $orderData = $order->getData();
               if (!empty($orderData)) {
                   $logger->info('Total Records found:' . count($orderData));
                   foreach ($orderData as $ord) {
                       $logger->info('Processing order id: ' . $ord['entity_id'] . " having status: " . $ord['status']);
                       if ($ord['status'] == "processing") {
                           $logger->info('Status of order:' . $ord['entity_id'] . " has not been changed for more than 10 minutes, need to closed.");
                           $this->createCM($ord['entity_id']);
                       }
                       if ($ord['status'] == "complete") {
                           $count = $this->checkPDF($ord['entity_id']);
                           if (!$count) {
                               $logger->info('Invalid order:' . $ord['entity_id'] . " with status complete, need to closed.");
                               $this->createCM($ord['entity_id']);
                           } else {
                               $logger->info('Valid order:' . $ord['entity_id'] . " cleaning not required.");
                           }
                       }
                   }
               }
           }catch (\Exception $e){
               $logger->info($e->getMessage());
           }
       }

    protected function checkPDF($ordId){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Clearkiosktestorders-cron-checkPDF.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $conn = $this->connection->getConnection();
        $countSql = "SELECT * FROM `sales_order_status_history` WHERE `parent_id` = '$ordId' and comment LIKE 'PDF Ticket SENT%'";
        $logger->info('Sql: '.$countSql);
        return $count = $conn->fetchOne($countSql);
    }

    protected function createCM($ordId){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Clearkiosktestorders-cron-createCM.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $order = $this->orderObj->load($ordId);
        if (!$order->getId()) {
            $logger->info('order does not exist');
        }
        if (!$order->canCreditmemo()) {
            $logger->info('cannot_create_creditmemo');
        }

        $data = array();

        /*$service = Mage::getModel('sales/service_order', $order);
        $creditmemo = $service->prepareCreditmemo($data);
        $creditmemo->setPaymentRefundDisallowed(true)->register();*/
        // add comment to creditmemo

    try {
        $creditmemo = $this->creditmemoFactory->createByOrder($order);
        $creditmemo->setPaymentRefundDisallowed(true);
        $creditmemo->save();
        $cmId = $creditmemo->getIncrementId();

        $notifyCustomer = 0;

        $comment = "Credit memo($cmId) created from kiosk cleanup cron";

        $order->addStatusHistoryComment($comment)
            ->setIsVisibleOnFront(false)
            ->setIsCustomerNotified(false);

           /* Mage::getModel('core/resource_transaction')
                ->addObject($creditmemo)
                ->addObject($order)
                ->save();*/

            $cmId = $creditmemo->getIncrementId();
            $comment = "Credit memo($cmId) created from kiosk cleanup cron";

            $order->addStatusHistoryComment($comment)
                ->setIsVisibleOnFront(false)
                ->setIsCustomerNotified(false);
            $order->save();

        } catch (\Exception $e) {
            $logger->info('Exception:'.$e->getMessage());
        }

    }

    public function getStoreId($code){
         $storeData = $this->store->load($code);
         return $storeData->getStoreId();
    }
  }