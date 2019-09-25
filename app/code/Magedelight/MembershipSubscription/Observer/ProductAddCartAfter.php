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

class ProductAddCartAfter implements ObserverInterface
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
     * @var type
     */
    protected $quoteFactory;

    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $quoteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Registry $registry,
        \Magento\Quote\Model\Quote\Item\OptionFactory $quoteFactory,
        array $data = []
    ) {
        $this->request = $request;
        $this->_objectManager = $objectmanager;
        $this->_cart = $cart;
        $this->_coreRegistry = $registry;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $product = $observer->getProduct();

        if ((!empty($product)) && ($product->getTypeId() == "Membership")) {
            $router = $this->request->getRouteName();
            $action = $this->request->getActionName();
            $controller = $this->request->getControllerName();
            $fullAction = $router . '_' . $controller . '_' . $action;
            
            if ($fullAction == "sales_order_reorder") {
                $durationOption = $this->getDurationOption();

                $durationOptionArray = unserialize($durationOption);

                if (count($durationOptionArray) > 0 && !empty($durationOptionArray['price'])) {
                    $item = $observer->getEvent()->getData('quote_item');
                    $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                    
                    $price = $durationOptionArray['price'];
                    $item->setCustomPrice($price);
                    $item->setOriginalCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);
                }
            } else {
                $item = $observer->getEvent()->getData('quote_item');
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

                $durationOption = $this->request->getParam('duration_option');

                $durationOptionArray = unserialize($durationOption);

                if (count($durationOptionArray) > 0 && !empty($durationOptionArray['price'])) {
                    $price = $durationOptionArray['price'];
                    $item->setCustomPrice($price);
                    $item->setOriginalCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);

                    $itemId = $item->getItemId();

                    $params[$itemId]['qty'] = 1;
                    $this->_cart->updateItems($params);
                }
            }
        }
    }

    /**
     * use for reorder
     *
     * @param type $item
     * @return type
     */
    public function getDurationOption()
    {
        $order = $this->_coreRegistry->registry('current_order');
        $items = $order->getItemsCollection();

        foreach ($items as $Currentitem) {
            $productType = $Currentitem->getProductType();
            $customOptions = [];

            $quoteItemId = $Currentitem->getQuoteItemId();

            if ((!empty($quoteItemId)) && ($productType == "Membership")) {
                $customOptions = $Currentitem->getData('product_options');
                $duration = $customOptions['info_buyRequest'];

                return $duration['duration_option'];
            }
        }
    }
}
