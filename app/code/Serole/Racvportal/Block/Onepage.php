<?php

  namespace Serole\Racvportal\Block;

  use Magento\Catalog\Api\CategoryRepositoryInterface;
  use Magento\Catalog\Block\Product\Context;
  use Magento\Catalog\Model\Layer\Resolver;
  use Magento\Framework\Data\Helper\PostHelper;
  use Magento\Framework\Url\Helper\Data;
  use Magento\Framework\View\Element\Template;

  class Onepage extends \Magento\Catalog\Block\Product\ListProduct{ //\Magento\Framework\View\Element\Template {

      protected $storeManager;

      protected $customerSession;

      protected $shops;

      protected $category;

      protected $pdfHelper;

      protected $helper;

      protected $logo;

      protected $checkoutSession;

      protected $helperOutput;

      protected $productFactory;

      public function __construct ( Context $context, PostHelper $postDataHelper, Resolver $layerResolver,
                                    CategoryRepositoryInterface $categoryRepository, Data $urlHelper,
                                   \Magento\Store\Model\StoreManagerInterface $storeManager,
                                   \Magento\Customer\Model\SessionFactory $customerSession,
                                   \Serole\Racvportal\Model\Ravportal $shops,
                                   \Magento\Catalog\Helper\Category $categoryHelper,
                                   \Magento\Catalog\Model\Category $category,
                                   \Serole\Pdf\Helper\Pdf $pdfHelper,
                                   \Serole\Racvportal\Helper\Data $helper,
                                   \Magento\Theme\Block\Html\Header\Logo $logo,
                                   \Magento\Checkout\Model\Session $checkoutSession,
                                   \Magento\Catalog\Helper\Output $helperOutput,
                                   \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
                                   array $data = [])
      {
          parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
          $this->storeManager = $storeManager;
          $this->customerSession = $customerSession->create();
          $this->shops = $shops;
          $this->category = $category;
          $this->_categoryHelper = $categoryHelper;
          $this->pdfHelper = $pdfHelper;
          $this->helper = $helper;
          $this->logo = $logo;
          $this->checkoutSession  = $checkoutSession;
          $this->helperOutput = $helperOutput;
          $this->productFactory = $productFactory;
      }

      public function getProductCollFactory(){
         return $this->productFactory;
      }

      public function getHelpOut(){
          return $this->helperOutput;
      }

      public function getStoreLogoUrl(){
          return $this->logo->getLogoSrc();
      }

      public function getStoreUrl(){
         return $this->storeManager->getStore()->getUrl();
      }

      public function getStoreId(){
          return $this->storeManager->getStore()->getId();
      }

      public function getMemberNo(){
          return $this->customerSession->getMemberNo();
      }

      public function getCustomerName(){
          $customerData = $this->customerSession->getCustomerData();
          return $customerData->getFirstname().' '.$customerData->getLastname();
      }

      public function getShopName(){
          $shopNo = $this->customerSession->getRacvShop();
          $shopObj = $this->shops->load($shopNo);
          return $shopObj->getName();
      }

      public function getLogoutUrl(){
          return $this->storeManager->getStore()->getUrl()."customer/account/logout";
      }

      public function getCategoryData($id){
          $categoryObj = $this->category->load($id);
          return $categoryObj->getData();
      }

      public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) {
          return $this->_categoryHelper->getStoreCategories($sorted = false, $asCollection = false, $toLoad = true);
      }

      public function getMediaUrl(){
          return $this->pdfHelper->getBaseStoreUrl();
      }

      public function getCategoryProducts($id){
          $productCollection = $this->category->load($id)->getProductCollection()->addAttributeToSelect('*');
          return $productCollection;
      }

      public function getAllShops(){
          $shopColl = $this->shops->getCollection();
          $shopColl->setOrder('name','ASC');
          return $shopColl->getData();
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
      public function getDefaultBaseUrl(){
          $this->helper->getBaseUrl();
      }

  }