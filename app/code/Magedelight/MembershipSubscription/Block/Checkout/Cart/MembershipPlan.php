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

namespace Magedelight\MembershipSubscription\Block\Checkout\Cart;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Customer\Model\Context;
use Magento\Checkout\Block\Cart;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class MembershipPlan extends AbstractCart
{
    /**
     *
     * @var type
     */
    protected $_item;
    
    /**
     *
     * @var OrderInterface
     */
    protected $_order;
    
    /**
     *
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    
    /**
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\Option $opction
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Model\ResourceModel\Quote\Item\Option $opction,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->_order = $order;
        $this->cart = $cart;
        $this->opction = $opction;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
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
     * @return boolean
     */
    public function getMembershipPlan()
    {
        
        foreach ($this->getItems() as $item) {
            $productType = $item->getProductType();
            
            if ($productType == "Membership") {
                foreach ($item->getOptions() as $option) {
                    $itemOptions = unserialize($option['value']);
                    
                    if (isset($itemOptions['duration_option'])) {
                        $currencySymbol = $this->getCurrentCurrencySymbol();
                        
                        $duration = unserialize($itemOptions['duration_option']);
                        
                        return $plan = $duration['duration']."&nbsp;".$duration['duration_unit']."&nbsp;-&nbsp;".$currencySymbol.$duration['price'];
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->getCustomItems()) {
            return $this->getCustomItems();
        }

        return parent::getItems();
    }
}
