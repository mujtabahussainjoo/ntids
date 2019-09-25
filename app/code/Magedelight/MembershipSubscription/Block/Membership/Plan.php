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

namespace Magedelight\MembershipSubscription\Block\Membership;

class Plan extends \Magento\Framework\View\Element\Template
{
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;
    
    /**
     * Membership factory
     *
     * @var MembershipOrdersFactory
     */
    protected $_MembershipOrdersFactory;
    
    /**
     *
     * @var Session
     */
    protected $customerSession;
    
    /**
     *
     * @var ProductFactory
     */
    protected $_productloader;
    
    /**
     *
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * Customer group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        array $data = []
    ) {
    
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->customerSession = $customerSession;
        $this->_productloader = $_productloader;
        $this->_priceCurrency = $priceCurrency;
        $this->_customerGroup = $customerGroup;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }
    
    /**
     *
     * @return array
     */
    public function getMembershipData()
    {
        $customerId = $this->getCurrentCustomer()->getId();
        if ($customerId) {
            $model = $this->_MembershipOrdersFactory->create()->getCollection();
            $model->addFieldToFilter('customer_id', $customerId);
            $model->addFieldToFilter('order_status', 'complete');
            $model->addFieldToFilter('plan_expiry_status', 0);
            $model->setOrder('membership_order_id', 'DESC');
            $data = $model->getData();
            if (count($data)>0) {
                return $data[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     *
     * @return boolean
     */
    public function getCurrentCustomer()
    {
        return $this->customerSession->getCustomer();
    }
    
    /**
     *
     * @return string
     */
    public function getCustomerGroup()
    {
        $currentGroup = $this->getCurrentCustomer()->getGroupId();
        $customerGroups = $this->_customerGroup->toOptionArray();
        foreach ($customerGroups as $group) {
            if ($group['value']==$currentGroup) {
                return $group['label'];
            }
        }
    }

    /**
     *
     * @param type $customerGroupId
     * @return boolean
     */
    public function getProduct($customerGroupId)
    {
        if ($customerGroupId) {
            $model = $this->_MembershipProductsFactory->create();
            $model->load($customerGroupId, 'customer_group_id');
            $productId = $model->getProductId();
            
            return $this->_productloader->create()->load($productId);
        }
        return false;
    }
    
    /**
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }
    
    /**
     *
     * @return array
     */
    public function getPaymentHistory()
    {
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 10;
        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 10;
        $customerId = $this->getCurrentCustomer()->getId();
        if ($customerId) {
            $collection = $this->_MembershipOrdersFactory->create()->getCollection();
            $collection->addFieldToFilter('customer_id', $customerId);
            $collection->setOrder('membership_order_id', 'DESC');
            $collection->getSelect()->join('magedelight_membership_products as products', 'main_table.membership_product_id = products.membership_product_id', 'product_name');
            $collection->setPageSize($pageSize);
            return $collection->setCurPage($page);
            
            $data = $collection->getData();
            return $data;
        }
    }
    
    /**
     *
     * @return \Magedelight\MembershipSubscription\Block\Membership\Plan
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        if ($this->getPaymentHistory()) {
                $pager = $this->getLayout()->createBlock(
                    'Magento\Theme\Block\Html\Pager',
                    'magecomp.category.pager'
                )->setAvailableLimit([10=>10])->setShowPerPage(true)->setCollection(
                    $this->getPaymentHistory()
                );

            $this->setChild('pager', $pager);
            $this->getPaymentHistory()->load();
        }
        return $this;
    }
    
    /**
     *
     * @return type
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    
    /**
     *
     * @return HTTP
     */
    public function getHref()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $url = trim($this->scopeConfig->getValue('membership/membership_settings/identifier', $storeScope));
        return $this->getUrl($url);
    }
    
    /**
     *
     * @param type $productId
     * @return type
     */
    public function getDiscountableProductsUrl($productId)
    {
        
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        return $baseUrl."md_membership/view/membership/id/".$productId;
    }

    /**
     *
     * @return type
     */
    public function getBeforeDay()
    {
        return $duration = (int)$this->scopeConfig->getValue('membership/general/mail_before_days', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
