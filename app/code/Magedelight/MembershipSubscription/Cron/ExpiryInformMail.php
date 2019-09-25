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

namespace Magedelight\MembershipSubscription\Cron;

class ExpiryInformMail
{
    /**
     * Membership factory
     *
     * @var MembershipOrdersFactory
     */
    protected $_MembershipOrdersFactory;
    
    /**
     * Membership factory
     *
     * @var MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;
    
    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var SenderFactory
     */
    protected $_emailSender;

    /**
     *
     * @var StoreManagerInterface
     */
    public $_storeManager;

    /**
     *
     * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magedelight\MembershipSubscription\Model\Email\SenderFactory $_emailSender
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magedelight\MembershipSubscription\Model\Email\SenderFactory $_emailSender,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->scopeConfig = $scopeConfig;
        $this->_emailSender = $_emailSender;
        $this->_storeManager = $storeManager;
    }
    
   
    public function execute()
    {
        $duration = (int)$this->scopeConfig->getValue('membership/general/mail_before_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($duration) {
            $shortDate = "+".$duration." Day";
            $increaseDate = strtotime($shortDate);
            $planExpiryDate = date("Y-m-d", $increaseDate);
//            $planExpiryDate = "2017-10-04";
        }
        
        
        $orderCollection = $this->_MembershipOrdersFactory->create()->getCollection();
        $orderCollection->addFieldToFilter('order_status', 'complete');
        $orderCollection->addFieldToFilter('plan_expiry_date', ['lteq' => $planExpiryDate]);
        $orderCollection->addFieldToFilter('plan_expiry_status', 0);
//        echo $orderCollection->getSelect()->__toString();
//        exit;
        $orders = $orderCollection->getData();
        
        if (count($orders)>0) {
            $expiryInfo  = [];
            
            foreach ($orders as $key => $value) {
                $customerEmail = $value['customer_email'];
                
                $planName = $this->getProductName($value['product_id']);
                $renew_url =  $this->getMembershipUrl();
                
                $expiryInfo['plan_name'] = $planName;
                $expiryInfo['plan_expiry_date'] = date("Y-m-d", strtotime($value['plan_expiry_date']));
                $expiryInfo['renew_url'] = $renew_url;
                
                $this->_emailSender->create()->sendReminderEmail($expiryInfo, $customerEmail);
            }
        }
    }
    
    
    /**
     *
     * @param type $productId
     * @return type
     */
    public function getProductName($productId)
    {
        $model = $this->_MembershipProductsFactory->create();
        $model->load($productId, 'product_id');
        return $model->getProductName();
    }
   
    /**
     *
     * @return string
     */
    public function getMembershipUrl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $urlKey = trim($this->scopeConfig->getValue('membership/membership_settings/identifier', $storeScope));
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $url = $baseUrl."/".$urlKey;
        return $url;
    }
}
