<?php

namespace Serole\Pdf\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;


class Pdfinfo extends Template {

    protected $order;

    protected $orderitemSerialcode;

    protected $orderData;

    protected $backendUrl;

    protected $serialcodes;

    protected $resourceConnection;

    protected $product;

    public function __construct(Template\Context $context,
                                \Magento\Sales\Model\Order $order,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Backend\Model\UrlInterface $backendUrl,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                \Serole\Serialcode\Model\OrderitemSerialcode $orderitemSerialcode,
                                array $data = [])
    {
        $this->orderData = array();
        $this->backendUrl = $backendUrl;
        $this->order = $order;
        $this->orderitemSerialcode = $orderitemSerialcode;
        $this->serialcodes = array();
        $this->resourceConnection = $resourceConnection;
        $this->product = $product;
        parent::__construct($context, $data);
    }

    public function generateUrl($action,$params){
        if(empty($params)){
            $url = $this->backendUrl->getUrl($action);
        }else{
            $url = $this->backendUrl->getUrl($action, $params);
        }
        return $url;
    }

    public function getOrderId(){
        return $this->getRequest()->getParam('order_id');
    }

    public function getOrderIncrementId(){
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($this->getOrderId());*/
        $order = $this->order->load($this->getOrderId());
        $this->orderData = $order->getData();
        return $order->getIncrementId();
    }

    public function getIncrementId(){
        return $this->orderData['increment_id'];
    }

    public function getQuoteId(){
        return $this->orderData['quote_id'];
    }

    public function isInvoiceCreated(){
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create('Magento\Sales\Model\Order')->load($this->getOrderId());*/
        $order = $this->order->load($this->getOrderId());
        $this->orderData = $order->getData();
        return $order->hasInvoices();
    }

    
    public function getSerialCodeData(){
        $incremetId = $this->orderData['increment_id'];
        /*$serialItemColl = $this->orderitemSerialcode->getCollection();
        $serialItemColl->addFieldToFilter('OrderID',$incremetId);
        $serialItemColl->addFieldToFilter('status',1);
        $this->serialcodes = $serialItemColl->getData();
        return $serialItemColl->getData();*/

        $connection = $this->resourceConnection->getConnection();
        $sql = "SELECT * FROM  `order_item_serialcode` WHERE  `OrderID` = ".$incremetId ." AND  `status` =1";
        $this->serialcodes = $connection->fetchAll($sql);
        return $this->serialcodes;

    }

    public function getOrderItems(){
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');*/
        $connection = $this->resourceConnection->getConnection();
        $sql = "select * from sales_order_item where order_id = ".$this->getOrderId();
        $results = $connection->fetchAll($sql);
        return $results;
    }

    public function generateSerialcodeString($sku,$parentsku){
        $serialCodeItems = $this->serialcodes;
        //print_r($serialCodeItems);
        $result = array_filter($serialCodeItems, function ($item) use ($sku,$parentsku) {
                if (stripos($item['sku'], $sku) !== false) {
                    if($parentsku) {
                        if (stripos($item['parentsku'], $parentsku) !== false) {
                            return true;
                        }
                    }else{
                        if(!$item['parentsku']){
                            return true;
                        }
                    }
                }
                return false;
            });
        $returnData = array();
        $returnData['count'] = count($result);
        $returnData['serialcode'] = implode('<br />', array_column($result, 'SerialNumber'));
        return $returnData;
    }

    public function isProductSerialised($productId){
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productObj = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);*/
        $productObj = $this->product->load($productId);
        return $productObj->getSerialno();
    }

}
