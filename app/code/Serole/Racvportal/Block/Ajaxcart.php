<?php
  namespace Serole\Racvportal\Block;

  use Magento\Framework\View\Element\Template;

  class Ajaxcart extends \Magento\Framework\View\Element\Template{

      protected $helper;

      protected $order;

      protected $resourceConnection;

      public function __construct(Template\Context $context,
                                  \Serole\Racvportal\Helper\Data $helper,
                                  \Magento\Sales\Model\Order $order,
                                  \Magento\Framework\App\ResourceConnection $resourceConnection,
                                  array $data = [])
      {
          parent::__construct($context, $data);
          $this->helper = $helper;
          $this->order =  $order;
          $this->resourceConnection = $resourceConnection;
      }

      public function getPdfFilePath($incrementId){
          $fileName = $incrementId.".pdf";
          $dirPath = $this->helper->getRootDir()."/neatideafiles/pdf/".$fileName;
          return $dirPath;
      }

      public function getPdfFileUrl(){
          $baseUrl = $this->helper->getStoreBaseUrl();
          return $baseUrl;
      }

      public function getOrdersData($params){
          //echo "<pre>"; print_r($params); exit;
          $date = date('Y-m-d'); //existing date
          $filterDate = date('Y-m-d', strtotime($date . '-2 weeks'));
          $storeId = $this->helper->getStoreId();
          $orderColl = $this->order->getCollection();
          $orderColl->addFieldToFilter('store_id', $storeId);
          $orderColl->addFieldToFilter('created_at', ['gteq' => $filterDate]);

          if ($params['customer'] == 'yes') {
              $orderColl->addFieldToFilter('customer_id', $this->helper->getCustomerId());
          }
          $orderColl->getSelect()->joinLeft(
              ['ot' => 'racvportal'],
              "main_table.increment_id = ot.increment_id"
          );
          $shopId = $this->helper->getShopId();
          if ($params['shop'] == 'yes') {
              $orderColl->getSelect()->where('ot.shop_id=' . $shopId);
          }

          $orderColl->getSelect()->order('main_table.increment_id DESC');

          if ($params['orderlist'] == 'no') {
              $orderColl->getSelect()->limit(1, 1);
          }
          $sql = $orderColl->getSelect();
          $connection = $this->resourceConnection->getConnection();
          $resultData = $connection->fetchAll($sql);
          //echo "<pre>"; print_r($resultData); exit;
          return $resultData;
      }

  }