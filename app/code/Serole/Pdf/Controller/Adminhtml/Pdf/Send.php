<?php

  namespace Serole\Pdf\Controller\Adminhtml\Pdf;

  use Magento\Backend\App\Action;


  class Send extends \Magento\Backend\App\Action{


      protected $orderObj;

      private $coreRegistry = null;

      private $resultPageFactory;

      private $backSession;

      private $pdfHelper;


      public function __construct(Action\Context $context,
                                  \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                  \Magento\Framework\Registry $registry,                                  
                                  \Serole\Pdf\Helper\Pdf $pdfHelper,
                                  \Magento\Sales\Model\Order $orderObj){

          $this->resultPageFactory = $resultPageFactory;
          $this->coreRegistry = $registry;
          $this->backSession = $context->getSession();
          $this->order = $orderObj;          
          $this->pdfHelper = $pdfHelper;
          parent::__construct($context);

      }


      public function execute(){
        $orderData  = $this->getRequest()->getParams();
        if(!isset($orderData['orderid'])){
            $this->messageManager->addError("OrderId Not Exists");
            $this->_redirect('*/*/');
            return;
        }
        try {
            $order = $this->order->loadByIncrementId($orderData['orderid']);
            $orderBaseId = $order->getId();
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            $giftMessageObj = $objManager->create('\Serole\GiftMessage\Model\Message')->getCollection();
            $giftMessageObj->addFieldToFilter('order_id',$orderData['orderid']);
            $giftMessageData = $giftMessageObj->getFirstItem()->getData();
            //echo "<pre>"; print_r($giftMessageData); exit;
            $customerData = array();
            if($giftMessageData){
                $giftImage = $objManager->create('\Serole\GiftMessage\Model\Image');
                $emailTemplateObj = $giftImage->load($giftMessageData['image']);
                $emailTemplateData = $emailTemplateObj->getData();
                $customerData['toname'] = $giftMessageData['to'];
                $customerEmail = $giftMessageData['email'];
                $customerData['message'] = $giftMessageData['message'];
                $customerData['from'] = $giftMessageData['from'];
                if($emailTemplateData){
                    $customerData['emailtemplateid'] = $emailTemplateData['emailtemplateid'];
                }
                //echo $customerEmail;
                //echo "<pre>"; print_r($customerData); exit;
            }else{
                $customerFirstName = $order->getCustomerFirstname();
                $customerLastName = $order->getCustomerLastname();
                $deliveryemail = $order->getdeliveryemail();
                if($deliveryemail){
                    $customerEmail = $deliveryemail;
                }else{
                    $customerEmail = $order->getCustomerEmail();
                }
                $customerData['toname'] = $customerFirstName . ' ' . $customerLastName;                
            }
            $customerData['email'] = $customerEmail;

            $pdfFileName = $orderData['orderid'] . '.pdf';
            $pdfFolderPath = $this->pdfHelper->getRootBaseDir().'neatideafiles/pdf';
            $pdfFilePath = $pdfFolderPath . '/' . $pdfFileName;
            if (!file_exists($pdfFilePath)) {
                $this->messageManager->addError("File Not exist, something went wrong");
                $this->_redirect('*/*/');
                return;
            }
            $mailStatus = $this->pdfHelper->sendPdfToCustomerEmail($pdfFilePath, $customerData, $order['incrementId']);
            if ($mailStatus) {
                $this->messageManager->addSuccess("Pdf Sent to customer");
            } else {
                $this->messageManager->addError("something went wrong");
            }
        }catch (\Exception $e){
            $this->messageManager->addError("something went wrong ".$e->getMessage());
        }
          return $this->_redirect($this->redirectUrl($orderBaseId));
      }

      public function redirectUrl($orderId){
          return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
      }

  }