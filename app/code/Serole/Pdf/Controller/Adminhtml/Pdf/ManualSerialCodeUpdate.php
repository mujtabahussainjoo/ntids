<?php

namespace Serole\Pdf\Controller\Adminhtml\Pdf;

use Magento\Backend\App\Action;


class ManualSerialCodeUpdate extends \Magento\Backend\App\Action{


    protected $orderObj;

    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    protected $sage;

    protected $messageManager;

    public function __construct(Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\Message\ManagerInterface $messageManager,
                                \Magento\Framework\Registry $registry,
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Serole\Sage\Model\Inventory $sage,
                                \Magento\Sales\Model\Order $orderObj){

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->backSession = $context->getSession();
        $this->order = $orderObj;
        $this->sage = $sage;
        $this->pdfHelper = $pdfHelper;
        $this->messageManager = $messageManager;
        parent::__construct($context);

    }


    public function execute(){
        $params  = $this->getRequest()->getParams();
        //echo "<pre>"; print_r($params);
        if(!$params['orderid'] || !$params['sku'] || !$params['missedSerialcodes'] || !$params['newserialcodes'] || $params['ishasparent'] == '' || !$params['quoteid']){
            //echo "<pre>"; print_r($params); exit;
            $this->messageManager->addErrorMessage('Some thing went wrong');
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        if($params['ishasparent'] == 1){
            if(!$params['parentsku']){
                $this->messageManager->addErrorMessage('Some thing went wrong with parent SKU');
                $this->_redirect($this->_redirect->getRefererUrl());
            }
        }

        $serialCodesExplode = explode(PHP_EOL,$params['newserialcodes']);
        if(empty($serialCodesExplode)){
            $this->messageManager->addErrorMessage('SerialCodes are empty');
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        if(count($serialCodesExplode) > $params['missedSerialcodes']){
            $this->messageManager->addErrorMessage('SerialCodes are not more that '.$params['missedSerialcodes']);
            //echo "Serial are over";
            $this->_redirect($this->_redirect->getRefererUrl());
        }

        $incrementId = $params['orderid'];
        $sku = $params['sku'];
        if($params['ishasparent']){
            $parentSku = $params['parentsku'];
        }else{
            $parentSku = '';
        }
        $quoteId = $params['quoteid'];
        $query = '';

        $sageReqData = array();
        foreach ($serialCodesExplode as $key => $serialCode){
			$serialCode = preg_replace('/\s+/', '', trim($serialCode));
            $sageReqData[$key] = "$quoteId,$incrementId,$sku,$serialCode";
        }
        $sageError = '';
        $sageResponse = $this->sage->CheckPhysicalSerialCode($sageReqData);
        //print_r($sageResponse); exit;
        //echo "<pre>"; print_r($sageResponse);
        if($sageResponse['status'] == 'Error'){
            if(!isset($sageResponse['msg']) || $sageResponse['msg'] == '') {
                $sageError = 'Error with SerialCode from sage ';
            }else{
                $sageError = $sageResponse['msg'];
            }
             if(isset($sageResponse['errorItem'])){
                foreach ($sageResponse['errorItem'] as $sku => $errorItem){
                   $sageError .= $sku.' -';
                   foreach ($errorItem  as $serialCode => $errorItemMessage){
                       $sageError .= $serialCode.' - '.$errorItemMessage.', ';
                   }
                }
             }
            $this->messageManager->addErrorMessage($sageError);
            return $this->_redirect($this->_redirect->getRefererUrl());
        }else {
            try {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('order_item_serialcode');

                foreach ($serialCodesExplode as $serialItem) {
                  $query .= "('$incrementId','$sku','$parentSku','$serialItem','1'),";
                }

                $queryClean = substr($query, 0, -1);

                $sql = "Insert Into " . $tableName . " (OrderID, sku, parentsku, SerialNumber,status) Values " . $queryClean . ";";

                $connection->query($sql);
                $this->messageManager->addSuccessMessage("Updated Sucess fully");
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                //return $this->_redirect($this->_redirect->getRefererUrl());
            }
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }


}