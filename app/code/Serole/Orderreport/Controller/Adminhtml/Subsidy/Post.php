<?php

   namespace Serole\Orderreport\Controller\Adminhtml\Subsidy;

   use \Magento\Backend\App\Action;

    class Post extends \Magento\Backend\App\Action {

        public function __construct(Action\Context $context,
                                    \Serole\Orderreport\Helper\Data $orderReportHelper,
                                    \Serole\Orderreport\Helper\Subsidy $subsidyReportHelper
                                   ){
            $this->orderReportHelper = $orderReportHelper;
            $this->subsidyReportHelper = $subsidyReportHelper;
            parent::__construct($context);
        }

        public function execute()
        {
            $params = $this->getRequest()->getParams();
            //echo "<pre>";print_r($params);exit;

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/report-subsidy-order-report-admin-controller.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            if (!$params['form_key']) {
                $this->messageManager->addError("Some thing Went wrong regrading form key");
                $this->_redirect('*/*/');
                return;
            }

            if (!$params['period']) {
                $this->messageManager->addError("Order Status value not found");
                $this->_redirect('*/*/');
                return;
            }

            if (empty($params['store_id'])) {
                $this->messageManager->addError("Store ID value not found");
                $this->_redirect('*/*/');
                return;
            }

            try {
                //echo "<pre>"; print_r($params); exit;
                $this->subsidyReportHelper->exportOrders($params);
            }catch (\Exception $e){
                $logger->info($e->getMessage());
                $this->messageManager->addError($e->getMessage());
                return $this->_redirect('*/*/');
            }
        }
    }