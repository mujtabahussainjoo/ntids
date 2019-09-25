<?php

namespace Serole\Orderreport\Controller\Adminhtml\Salesorder;

use Magento\Backend\App\Action;


class Post extends \Magento\Backend\App\Action {

    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    private $order;
   

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,        
        \Serole\Orderreport\Helper\Data $orderReportHelper,
        \Magento\Store\Model\Store $store        
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;          
        $this->orderReportHelper = $orderReportHelper;        
        $this->backSession = $context->getSession();
        $this->order = $order;
        $this->store = $store;
        parent::__construct($context);
    }

    public function execute(){
        $params = $this->getRequest()->getParams();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-sales-order-report-admin-controller.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if(!$params['form_key']){
            $this->messageManager->addError("Some thing Went wrong regrading form key");
            $this->_redirect('*/*/');
            return;
        }

        if(!$params['order_status']){
            $this->messageManager->addError("Order Status value not found");
            $this->_redirect('*/*/');
            return;
        }

        if(empty($params['from_date']) || empty($params['from_date'])){
            echo "Exception";
        }
      try {
          if ($params['order_status'] == 'all') {
              $orderCollection = $this->order->getCollection();
              $orderCollection->addAttributeToSelect('*');
              $orderCollection->addAttributeToFilter('created_at', array('from' => $params['from_date'] . ' 00:00:01', 'to' => $params['to_date'] . ' 23:59:59',));
			  if ($params['store_id']!='ALLSTORES') {
				  //exit('Statul All-----Not ALLSTORES');
                  $orderCollection->addAttributeToFilter('store_id', $params['store_id']);
              }
              $orderCollection->addAttributeToSort('entity_id', 'ASC');
          } else {
              $orderCollection = $this->order->getCollection();
              $orderCollection->addAttributeToSelect('*');
              $orderCollection->addAttributeToFilter('created_at', array('from' => $params['from_date'] . ' 00:00:01', 'to' => $params['to_date'] . ' 23:59:59',));
              if ($params['store_id']!='ALLSTORES') {
				  //exit('Statul Specified-----Not ALLSTORES');
                  $orderCollection->addAttributeToFilter('store_id', $params['store_id']);
              }
              $orderCollection->addAttributeToFilter('status', $params['order_status']);
              $orderCollection->addAttributeToSort('entity_id', 'ASC');
          }
          //echo "<pre>"; print_r($orderCollection->getData());
          if($params['file_type'] == 'pdf'){
              $file = $this->orderReportHelper->exportsalesorderPDFOrders($orderCollection, $params);
          }else{
              $file = $this->orderReportHelper->exportsalesorderCSVOrders($orderCollection, $params);
          }
          //$this->messageManager->addSuccess("File is downloading");
          //$this->_redirect('*/*/');

      }catch(\Exception $e){
          $logger->info($e->getMessage());
          $this->messageManager->addError("Error".$e->getMessage());
          $this->_redirect('*/*/');
          return;
      }
    }

}