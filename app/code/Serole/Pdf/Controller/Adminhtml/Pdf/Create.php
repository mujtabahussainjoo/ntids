<?php

namespace Serole\Pdf\Controller\Adminhtml\Pdf;

use Magento\Backend\App\Action;


class Create extends \Magento\Backend\App\Action{


    protected $orderObj;

    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    private $pdfHelper;

    private $createPdf;


    public function __construct(Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\Registry $registry,                                
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Serole\Pdf\Model\Createpdf $createPdf,
                                \Magento\Sales\Model\Order $orderObj){

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->backSession = $context->getSession();
        $this->order = $orderObj;        
        $this->pdfHelper = $pdfHelper;
        $this->createPdf = $createPdf;
        parent::__construct($context);

    }


    public function execute(){
        $orderData  = $this->getRequest()->getParams();
        //print_r($orderData);
        //exit;
        if(!isset($orderData['orderid'])){
            $this->messageManager->addError("OrderId Not Exists");
            $this->_redirect('*/*/');
            return;
        }
        try {
            if(isset($orderData['email'])){
                if($orderData['type'] == 'backend'){
                    $status = $this->createPdf->createPdfConcept($orderData['orderid'],$email=TRUE,'backend');
                }else{
                    $status = $this->createPdf->createPdfConcept($orderData['orderid'],$email=TRUE,'frontend');
                }
                if($status['status'] != 'error'){
                    $this->messageManager->addSuccess("Pdf Generated & sent to customer");
                }else{
                    $errorarray = implode(',',$status['message']);
                    $this->messageManager->addError($errorarray);
                }
            }else{
                if($orderData['type'] == 'backend'){
                    $status = $this->createPdf->createPdfConcept($orderData['orderid'],$email=FALSE,'backend');
                }else{
                    $status = $this->createPdf->createPdfConcept($orderData['orderid'],$email=FALSE,'frontend');
                }
                if($status){
                    $this->messageManager->addSuccess("Pdf Generated ");
                }else{
                    $this->messageManager->addError("something went wrong ");
                }
            }

        }catch (\Exception $e){
            $this->messageManager->addError("something went wrong ".$e->getMessage());
        }
        //return $this->_redirect($this->redirectUrl($orderData['orderid']));
        //$this->_redirect('*/*/');
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    public function redirectUrl($orderId){
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

}