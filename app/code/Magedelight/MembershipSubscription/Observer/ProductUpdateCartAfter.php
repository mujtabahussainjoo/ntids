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

namespace Magedelight\MembershipSubscription\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class ProductUpdateCartAfter implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager;


    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        $this->request = $request;
        $this->_objectManager = $objectmanager;
        $this->_cart = $cart;
    }
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        
        if ((!empty($product)) && ($product->getTypeId() == "Membership")) {
            $durationOption = $this->request->getParam('duration_option');

            $durationOptionArray = unserialize($durationOption);
            if (count($durationOptionArray)>0 && !empty($durationOptionArray['price'])) {
                // get item form quote item
                $item = $observer->getEvent()->getData('quote_item');
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

                $price = $durationOptionArray['price'];
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);
                $item->getProduct()->setIsSuperMode(true);
                
                // set item quote_item_option
                foreach ($item->getOptions() as $option) {
                    $itemOptions = unserialize($option['value']);
                    $durationOption = $this->request->getParam('duration_option');
                    $itemOptions['duration_option'] = $durationOption;
                    $option->setValue(serialize($itemOptions));
                }

                $itemId= $item->getItemId();
                $params[$itemId]['qty'] = 1;
                $this->_cart->updateItems($params);
                $this->_cart->saveQuote();
                $this->_cart->save();
            }
        }
    }
}
