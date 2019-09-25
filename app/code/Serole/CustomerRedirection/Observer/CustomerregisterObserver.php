<?php

namespace Serole\CustomerRedirection\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerregisterObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;

    /**
     * @param  \Magento\Framework\App\ResponseFactory $responseFactory
     */

    protected $_url;

    /**
     * @param \Serole\CustomerRedirection\Helper\Data $helper
     *
     */
    protected $_helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface $url,
        \Serole\CustomerRedirection\Helper\Data $helper
    ) {
        $this->_objectManager = $objectManager;
        $this->_url = $url;
        $this->_helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customer = $observer->getEvent()->getData('customer');
            $customerGroup = $customer->getGroupId();

            //getting config values
            $pluginStatus = $this->_helper->getConfig('customredirectionsection/general/active');
            $configCustomerGroup = $this->_helper->getConfig('customredirectionsection/customer_redirection_grp/customer_group');
            $regRdnStatus = $this->_helper->getConfig
            ('customredirectionsection/register_redirection_grp/register_redirection');
            $rdnPath = $this->_helper->getConfig('customredirectionsection/register_redirection_grp/register_redirection_path');


            $CustomRedirectionUrl = $this->_url->getUrl($rdnPath);

            if($pluginStatus){
                if($regRdnStatus){
                    if($configCustomerGroup){
                        if($configCustomerGroup == $customerGroup){
                            $this->_redirect($CustomRedirectionUrl);
                        }
                    }else{
                        $this->_redirect($CustomRedirectionUrl);
                    }
                }
            }


        }catch (Exception $e){
            return false;
        }
    }

    /**
     *
     * @param $url
     * @return void
     *
     */
    protected function _redirect($url){
       $this->_objectManager->get('Magento\Customer\Model\Session')
            ->setBeforeAuthUrl($url);//->setRedirectOnSignup();
       // $this->_response->setRedirect($url)->sendResponse();
        //response->setRedirect($url);
    }
}
