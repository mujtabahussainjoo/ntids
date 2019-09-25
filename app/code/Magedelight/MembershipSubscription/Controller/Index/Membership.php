<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Controller\Index;

class Membership extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\DataObject                      $requestObject
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session                    $customerSession
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\DataObject $requestObject,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_requestObject = $requestObject;
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
    
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
//        $ObjectManager= \Magento\Framework\App\ObjectManager::getInstance();
//        $context = $ObjectManager->get('Magento\Framework\App\Http\Context');
//        $isLoggedIn = $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        
//        if ($isLoggedIn) {
            return $resultPage;
//        } else {
//            $resultRedirect = $this->resultRedirectFactory->create();
//
//            return $resultRedirect->setPath('customer/account/login');
//        }
    }
}
