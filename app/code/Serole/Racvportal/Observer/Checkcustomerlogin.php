<?php
namespace Serole\Racvportal\Observer;

use Magento\Framework\Event\ObserverInterface;

class Checkcustomerlogin implements ObserverInterface
{

    private $scopeConfig;

    private $customerSession;

    private $customerUrl;

    private $context;

    private $contextHttp;

    protected $storeManager;

    protected $responseFactory;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $contextHttp,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->context         = $context;
        $this->customerSession = $customerSession;
        $this->customerUrl     = $customerUrl;
        $this->contextHttp     = $contextHttp;
        $this->storeManager = $storeManager;
        $this->responseFactory = $responseFactory;
    }


    public function execute(\Magento\Framework\Event\Observer $observer){
        $event_name = $observer->getEventName();
        $storeCode = $this->storeManager->getStore()->getCode();
        $module_name     = $this->context->getRequest()->getModuleName();
        $controller_name = $this->context->getRequest()->getControllerName();
        $action_name     = $this->context->getRequest()->getActionName();

      if($storeCode == 'racvportal'){
            if(($module_name == 'cms' && $controller_name == 'index' && $action_name == 'index') || ($module_name == 'racvportal')){
                if (!$this->customerSession->isLoggedIn()) {
                    $customer_login_url = $this->customerUrl->getLoginUrl();
                    $this->responseFactory->create()->setRedirect($customer_login_url)->sendResponse();
                    return $this;
                }
            }else if($module_name == 'customer' && $controller_name == 'account' && $action_name == 'index') {
                if (!$this->customerSession->isLoggedIn()) {
                    $customer_login_url = $this->customerUrl->getLoginUrl();
                    $this->context->getResponse()->setRedirect($customer_login_url);
                    return $this;
                }else {
                    $storeBaseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $this->context->getResponse()->setRedirect($storeBaseUrl);
                    return $this;
                }
            }else if($module_name != 'customer' && $controller_name != 'account' && $action_name != 'login') {
                if (!$this->customerSession->isLoggedIn()) {
                    $customer_login_url = $this->customerUrl->getLoginUrl();
                    $this->context->getResponse()->setRedirect($customer_login_url);
                    return $this;
                }else {
                    $storeBaseUrl = $this->storeManager->getStore()->getBaseUrl();
                    $this->context->getResponse()->setRedirect($storeBaseUrl);
                    return $this;
                }
            }
      }
    }
}
