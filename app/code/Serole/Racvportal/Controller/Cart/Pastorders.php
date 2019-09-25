<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Pastorders extends \Serole\Racvportal\Controller\Cart\Ajax {

    public function execute(){
         try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-pastorders.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
             if ($this->getRequest()->isAjax()) {
                 /*$order = $this->order->loadByIncrementId(64000314859);
                 $logger->info("start");
                 $logger->info($order->hasInvoices());
                 $logger->info("end");*/
                 $date = date('Y-m-d');
                 $filterDate = date('Y-m-d', strtotime($date . '-2 weeks'));
                 $parms = $this->getRequest()->getParams();
                 $storeId = $this->store->getStore()->getId();
                 $orderColl = $this->order->getCollection();
                 $orderColl->addFieldToFilter('store_id', $storeId);
                 $orderColl->addFieldToFilter('status', 'complete');
                 $orderColl->addFieldToFilter('created_at', ['gteq' => $filterDate]);

                 if ($parms['customer'] == 'yes') {
                     $orderColl->addFieldToFilter('customer_id', $this->helper->getCustomerId());
                 }

                 $orderColl->getSelect()->joinLeft(
                     ['ot' => 'racvportal'],
                     "main_table.increment_id = ot.incrementid"
                 );
                 $shopId = $this->helper->getShopId();

                 if ($parms['shop'] == 'yes') {
                     $orderColl->getSelect()->where('ot.shop_id=' . $shopId);
                 }

                 $orderColl->getSelect()->order('main_table.increment_id DESC');

                 if ($parms['order'] == 'no') {
                     $orderColl->getSelect()->limit(1);
                 }

              /* $sql = $orderColl->getSelect();
                 $logger->info($sql);
                 $connection = $this->resourceConnection->getConnection();
                 $resultData = $connection->fetchAll($sql);
                 $logger->info($resultData);
                 $logger->info("-------");*/
                 $resultData = $orderColl->getData();
                 $logger->info($resultData);

                 $resultPage = $this->resultPageFactory->create();
                 
                 if ($parms['order'] == 'no') {
                     $block = $resultPage->getLayout()
                         ->createBlock('Serole\Racvportal\Block\Onepage')
                         ->setTemplate('Serole_Racvportal::singleorder.phtml')
                         ->setData('data', $resultData);
                 }else{
                      $block = $resultPage->getLayout()
                         ->createBlock('Serole\Racvportal\Block\Onepage')
                         ->setTemplate('Serole_Racvportal::pastorders.phtml')
                         ->setData('data', $resultData);
                 }    

                 $htmlResponse = $block->toHtml();
                 $data['html'] = $htmlResponse;
                 $data['status'] = 'sucess';
                 $data['customersession'] = 'yes';
                 ob_start();
                 echo json_encode($data);
             }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $data['status'] = 'error';
            $data['customersession'] = 'yes';
            $data['message'] = $e->getMessage();
            echo json_encode($data);
        }
    }
}