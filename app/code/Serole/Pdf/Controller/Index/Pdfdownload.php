<?php
  namespace Serole\Pdf\Controller\Index;

  use Magento\Framework\App\Action\Context;

  class Pdfdownload extends \Magento\Framework\App\Action\Action {

      protected $customerSession;

      protected $order;

      protected $pdfHelper;

      public function __construct(Context $context,
                                  \Magento\Sales\Model\Order $order,
                                  \Serole\Pdf\Helper\Pdf $pdfHelper,
                                  \Magento\Customer\Model\Session $customerSession
                                  )
      {
          $this->customerSession = $customerSession;
          $this->order = $order;
          $this->pdfHelper = $pdfHelper;
          parent::__construct($context);
      }

      public function execute(){
          $incrementId = $this->getRequest()->getParam('id');
          if($incrementId) {
              $customerId = $this->customerSession->getCustomer()->getId();
              if($customerId) {
                  $order = $this->order->loadByIncrementId($incrementId);
                  $orderCustomerId = $order->getCustomerId();
                  if((int)$orderCustomerId === (int)$customerId) {
                      $rootDir = $this->pdfHelper->getRootBaseDir();
                      $pdfFilesPath = $rootDir . 'neatideafiles/pdf/';
                      $pdfFileName = $incrementId.".pdf";
                      $filepath = $pdfFilesPath.$pdfFileName;
                      if (file_exists($filepath)) {
                          header("Content-Type: application/octet-stream");
                          header("Content-Disposition: attachment; filename=" . urlencode($pdfFileName));
                          header("Content-Type: application/octet-stream");
                          header("Content-Type: application/download");
                          header("Content-Description: File Transfer");
                          header("Content-Length: " . filesize($filepath));
                          flush(); // this doesn't really matter.
                          $fp = fopen($filepath, "r");
                          while (!feof($fp)) {
                              echo fread($fp, 65536);
                              flush(); // this is essential for large downloads
                          }
                          fclose($fp);
                      }else{
                          echo "<script>alert('Please contact us')</script>";
                          echo "<script>window.close();</script>";
                      }
                  }else{
                      echo "<script>alert('No Access')</script>";
                      echo "<script>window.close();</script>";
                  }
              }else{
                  echo "<script>alert('Please login')</script>";
                  echo "<script>window.close();</script>";
              }
          }
      }
  }