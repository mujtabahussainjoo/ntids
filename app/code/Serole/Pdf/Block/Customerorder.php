<?php

namespace Serole\Pdf\Block;

 use Magento\Customer\Model\Context;


   class Customerorder extends \Magento\Framework\View\Element\Template {

       public $pdfHelper;

       public function __construct(
           \Magento\Framework\View\Element\Template\Context $context,
           \Magento\Framework\Registry $registry,
           \Magento\Framework\App\Http\Context $httpContext,
           \Serole\Pdf\Helper\Pdf $pdfHelper,
           array $data = []
       ) {
           $this->_coreRegistry = $registry;
           $this->httpContext = $httpContext;
           $this->pdfHelper = $pdfHelper;
           parent::__construct($context, $data);
           $this->_isScopePrivate = true;
       }

       public function getOrder(){
           return $this->_coreRegistry->registry('current_order');
       }

       public function getPdfFile(){
		$incrementId = $this->getOrder()->getIncrementId();
		$fileName = $incrementId.'.pdf';
		$fileBasePath = $this->pdfHelper->getRootBaseDir();
		$baseUrl = $this->pdfHelper->getBaseStoreUrl();
		$file['filePath'] = $fileBasePath."neatideafiles/pdf/".$fileName;
		$file['fileUrl'] = $baseUrl."neatideafiles/pdf/".$fileName;
		$file['incrementid'] = $incrementId;
		return $file;
      }

       public function getIncrementId(){
           echo $this->pdfHelper->getBaseStoreUrl();
           return $this->getOrder()->getIncrementId();
       }
   }
