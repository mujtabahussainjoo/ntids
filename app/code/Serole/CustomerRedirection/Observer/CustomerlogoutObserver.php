<?php

namespace Serole\CustomerRedirection\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Symfony\Component\Config\Definition\Exception\Exception;

class CustomerlogoutObserver implements ObserverInterface
{
    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $_objectManager;

    /**
     * @param  \Magento\Framework\App\ResponseFactory $responseFactory
     */
    protected $_responseFactory;

    /**
     * @param \Magento\Framework\UrlInterface $url
     */
    protected $_url;

    /**
     * @param \Serole\CustomerRedirection\Helper\Data $helper
     *
     */
    protected $_helper;

    /*
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    protected $_response;

    /**
     * CustomerlogoutObserver constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Serole\CustomerRedirection\Helper\Data $helper
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Serole\CustomerRedirection\Helper\Data $helper,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->_objectManager = $objectManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_helper = $helper;
        $this->_response = $response;
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
            $lgoutnRdnStatus = $this->_helper->getConfig
            ('customredirectionsection/logout_redirection_grp/logout_redirection');
            $rdnPath = $this->_helper->getConfig('customredirectionsection/logout_redirection_grp/logout_redirection_path');

            $CustomRedirectionUrl = $this->_url->getUrl($rdnPath);

            if($pluginStatus){
                if($lgoutnRdnStatus){
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
        $this->_objectManager->get
        ('Magento\Customer\Model\Session')
            ->unsCustomerId();
       $this->_response->setRedirect($url)
            ->sendResponse();
        exit;
    }
}
