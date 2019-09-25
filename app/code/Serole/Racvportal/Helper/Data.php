<?php

  namespace Serole\Racvportal\Helper;

  use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{

    protected $shops;

    protected $customerSession;

    protected $directoryList;

    protected $scopeConfig;

    protected $storeManager;

    protected $coreSession;

    public function __construct(Context $context,
                                \Magento\Customer\Model\SessionFactory $customerSession,
                                \Magento\Framework\Filesystem\DirectoryList $directoryList,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Framework\Session\Generic $coreSession,
                                \Serole\Racvportal\Model\Ravportal $shops)
    {
        parent::__construct($context);
        $this->shops = $shops;
        $this->directoryList = $directoryList;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->coreSession = $coreSession;
        $this->customerSession = $customerSession->create();

    }

    public function getShopId(){
        return $this->customerSession->getRacvShop();
    }

    public function getShopName(){
        $shopNo = $this->getShopId();
        $shopObj = $this->shops->load($shopNo);
        return $shopObj->getName();
    }

    public function getShopData(){
        $shopNo = $this->getShopId();
        $shopObj = $this->shops->load($shopNo);
        //$shopObj->addFieldToFilter('store_id',$this->getStoreId());
        return $shopObj->getData();
    }

    public function isCustomerLoggedIn(){
        return $this->customerSession->isLoggedIn();
    }

    public function getCustomerId(){
        return $this->customerSession->getCustomerId();
    }

    public function getRootDir(){
       return $this->directoryList->getRoot();
    }

    public function getBaseDir(){
        return $this->directoryList->getPath('var');
    }

    public function getCustomerName(){
        $customerData = $this->customerSession->getCustomerData();
        return $customerData->getFirstname().' '.$customerData->getLastname();
    }

    public function getMediaBaseUrl(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue('web/secure/base_media_url',$storeScope);
    }

    public function getStoreId(){
        return $this->storeManager->getStore()->getId();
    }

    public function getStoreBaseUrl(){
        return $this->storeManager->getStore()->getUrl();
    }

    public function getBaseUrl(){
        return $this->scopeConfig->getValue('web/secure/base_url','default');
    }


}