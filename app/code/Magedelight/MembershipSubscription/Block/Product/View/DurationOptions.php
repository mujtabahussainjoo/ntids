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

namespace Magedelight\MembershipSubscription\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session\Proxy as Customersession;

class DurationOptions extends Template
{

    /**
     * @var Exercises\Vendor\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;

    /**
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     *
     * @var Url
     */
    protected $_customerUrl;
   
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param Customersession $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\Url $customerUrl,
        Customersession $customerSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->productFactory = $productFactory;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->_customerUrl = $customerUrl;
        $this->session = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\SessionFactory
     */
    public function getCustomerSession()
    {
        return  $this->session->isLoggedIn();
    }
    
    /**
     *
     * @return string
     */
    public function getCustomerLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('current_product');
        return $product ? $product->getId() : null;
    }
    
    
    /**
     * Get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }
    
    /**
     *
     * @return type
     */
    public function getDurationOption()
    {

        $currencySymbol = $this->getCurrentCurrencySymbol();

        $durationOption = [];
        
        $durationArray = $this->getDurationArray();
        
        if (count($durationArray)>0) {
            foreach ($durationArray as $key => $value) {
                $durationOption[$key]['price'] = $currencySymbol.$value['price'];
                $durationOption[$key]['label'] = $value['duration']."&nbsp;".$value['duration_unit']." - ".$currencySymbol.$value['price'];
                $durationOption[$key]['value'] = serialize($durationArray[$key]);
            }
        }
        
        return $durationOption;
    }
    
    /**
     *
     * @return array
     */
    public function getDurationArray()
    {
        $productId = $this->getProductId();
        
        if ($productId) {
            $model = $this->_MembershipProductsFactory->create();
            $model->load($productId, 'product_id');
            $membershipDuration = $model->getMembershipDuration();
            
            $durationArray = unserialize($membershipDuration);
            if (count($durationArray)>0) {
                $newDurations = $this->arraySortByColumn($durationArray, 'sort_order', SORT_ASC);
                if (count($newDurations)>0) {
                    return $newDurations;
                }
            }
        }
    }
    
    /**
     *
     * @param type $array
     * @param type $column
     * @param type $sort
     * @return type
     */
    public function arraySortByColumn(&$array, $column, $sort)
    {
        $count = [];
        foreach ($array as $key => $row) {
            $count[$key] = $row[$column];
        }

        array_multisort($count, $sort, $array);
        
        return $array;
    }
}
