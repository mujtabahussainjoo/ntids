<?php

 namespace Serole\GiftMessage\Block;

 use Magento\Customer\Model\Context;

 class Cart extends \Magento\Checkout\Block\Cart\AbstractCart
 {

     protected $giftEmailImages;

     protected $_cartHelper;

     protected $_catalogUrlBuilder;

     protected $httpContext;

     protected $cartHelper;

     protected $product;

     protected $giftImage;

     protected $pdfHelper;

     protected $scopeConfig;

     public function __construct(
         \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Customer\Model\Session $customerSession,
         \Magento\Checkout\Model\Session $checkoutSession,
         \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
         \Magento\Checkout\Helper\Cart $cartHelper,
         \Magento\Framework\App\Http\Context $httpContext,
         \Magento\Catalog\Model\Product $product,
         \Serole\Pdf\Helper\Pdf $pdfHelper,
         \Serole\GiftMessage\Model\Image $giftImage,
         \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         array $data = []
     ) {
         $this->_cartHelper = $cartHelper;
         $this->_catalogUrlBuilder = $catalogUrlBuilder;
         $this->product = $product;
         $this->giftImage = $giftImage;
         $this->pdfHelper = $pdfHelper;
         $this->customerSession = $customerSession;
         parent::__construct($context, $customerSession, $checkoutSession, $data);
         $this->_isScopePrivate = true;
         $this->httpContext = $httpContext;
         $this->scopeConfig = $scopeConfig;
     }

     public function moduleStatus(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue("giftmessage/general/enable", $storeScope);
     }

     public function getQuote(){
         if (null === $this->_quote) {
             $this->_quote = $this->_checkoutSession->getQuote();
         }
         return $this->_quote;
     }

     public function getItemsCount(){
         return $this->getQuote()->getItemsCount();
     }

     public function getIsVirtual(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Session')->getQuote();
        $cartItems = $cart->getAllItems();
        $isVirtualProductHas = 0;
        foreach ($cartItems as $cartItem) {
            if($cartItem->getIsVirtual() == 1){
               $isVirtualProductHas = 1;
               break;
            }           
        }       
         //return $this->_cartHelper->getIsVirtualQuote();
        return $isVirtualProductHas;
     }

     public function isGiftMessageProductExist(){
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $isGiftMessageProduct = '';
         foreach ($this->getItems() as $item) {
             $product = $item->getProduct();             
             $productObj = $this->product->load($product->getId());
             if ($productObj->getIsGiftMessage() && ($productObj->getTypeId() == 'virtual'|| $productObj->getTypeId() == 'bundle')) {
                 $isGiftMessageProduct = 1;
                 break;
             }
         }
         return $isGiftMessageProduct;
     }

     public function getGiftEmailTemplateImages(){
         $giftEmailTemplateColl = $this->giftImage->getCollection();
         return $giftEmailTemplateColl->getData();
     }

    public function getImagePath($imageName){
         $giftMessageImageFolderName = 'giftimagestemplates';
         $filePath = $this->pdfHelper->getRootBaseDir().$giftMessageImageFolderName.'/'.$imageName; //->getFilePath($giftMessageImageFolderName, $imageName);
         return $filePath;
     }

     public function getImageurl($imageName){
         $giftMessageImageFolderName = 'giftimagestemplates';        
         $defaultUrl = $this->pdfHelper->getDefaultBaseUrl();
         //$fileUrl = $this->pdfHelper->getFileUrl($giftMessageImageFolderName, $imageName);
         $fileUrl = $defaultUrl.$giftMessageImageFolderName."/".$imageName;
         return $fileUrl;
     }

     public function getCustomerSession(){
         $customerSession = $this->customerSession;
         return $customerSession;
     }

     public function getEmailTemplateName($emailTemplateId){
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $emailTemplateObj = $objectManager->create('\Magento\Email\Model\Template')->load($emailTemplateId);
         return $emailTemplateObj->getTemplateSubject();
     }

 }