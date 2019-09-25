<?php
namespace Serole\ForcedLogin\Observer;

use Magento\Framework\Event\ObserverInterface;

class ForceCustomerLoginObserver implements ObserverInterface
{

    private $scopeConfig;

    private $customerSession;

    private $customerUrl;

    private $context;

    private $contextHttp;


    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $contextHttp,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->scopeConfig     = $scopeConfig;
        $this->context         = $context;
        $this->customerSession = $customerSession;
        $this->customerUrl     = $customerUrl;
        $this->contextHttp     = $contextHttp;
    }//end __construct()


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event_name = $observer->getEventName();

        $forced_login_status = $this->scopeConfig->getValue(
            'forcedlogin/parameters/status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $forced_login_access = $this->scopeConfig->getValue(
            'forcedlogin/parameters/access_to_website',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($forced_login_status) {
            $module_name     = $this->context->getRequest()->getModuleName();
            $controller_name = $this->context->getRequest()->getControllerName();
            $action_name     = $this->context->getRequest()->getActionName();

            $actionUrl = $module_name.'/'.$controller_name.'/'.$action_name;

            $isLoggedIn = $this->contextHttp->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

            if ($isLoggedIn || $module_name === 'api') {
                return $this;
            }

            /*
             if ($controller_name === 'account' && $forced_login_access === '1') {
                return $this;
            }

            if ($forced_login_access === '0' && $controller_name === 'account'
                && ($action_name === 'login' || $action_name === 'loginPost'
                || $action_name === 'forgotpassword' || $action_name === 'createpassword')
                || $actionUrl == 'cms/index/index'
            ) {
                return $this;
            }*/
            //echo $forced_login_access;
            //echo $module_name; exit;
            if(($module_name === 'catalog' || $module_name === 'cashback') && $forced_login_access === '1'){
                $customer_login_url = $this->customerUrl->getLoginUrl();
                $this->context->getResponse()->setRedirect($customer_login_url);
               return $this;
            }elseif(($module_name === 'catalogsearch' || $module_name === 'cashback') && $forced_login_access === '1'){
			    $customer_login_url = $this->customerUrl->getLoginUrl();
                $this->context->getResponse()->setRedirect($customer_login_url);
               return $this;
			}


        }//end if

        //return $this;
    }//end execute()
}//end class
