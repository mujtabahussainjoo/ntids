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

class OrderPlaceAfter implements ObserverInterface
{
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     *
     * @var session
     */
    protected $session;
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;

    /**
     *
     * @var MembershipOrdersFactory
     */
    protected $_MembershipOrdersFactory;
    /**
     * @var Cart
     */
    protected $_cart;

    /**
     *
     * @var OrderInterface
     */
    protected $_order;
    
    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $session,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Sales\Api\Data\OrderInterface $order,
        array $data = []
    ) {
        $this->request = $request;
        $this->session = $session;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->_cart = $cart;
        $this->_order = $order;
    }
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderids = $observer->getEvent()->getOrderIds();

        foreach ($orderids as $orderid) {
            $order = $this->_order->load($orderid);
            $currentOrder = $order->getData();
            
            $itemCollection = $order->getItemsCollection()->getData();
            
            foreach ($itemCollection as $item) {
                if ((!empty($item)) && ($item['product_type'] == "Membership")) {
                    $productId = $item['product_id'];
                    
                    $orderStatus = $order->getStatus();
                    
                    if ($productId) {
                        $membershipProduct = $this->getMembershipProduct($productId);
                        
                        $product_options = unserialize($item['product_options']);
                        $customer_plan = $product_options['info_buyRequest']['duration_option'];
                        
                        $data = ['membership_product_id'=>$membershipProduct['membership_product_id'],
                                'product_id'=>$productId,
                                'order_id'=>$item['order_id'],
                                'order_status'=>$currentOrder['status'],
                                'customer_id'=>$currentOrder['customer_id'],
                                'customer_email'=>$currentOrder['customer_email'],
                                'customer_plan'=>$customer_plan,
                                'price'=>$item['price'],
                                'related_customer_group_id'=>$membershipProduct['related_customer_group_id']];
                        
                        if (count($data)>0) {
                            $model = $this->_MembershipOrdersFactory->create();
                            $model->addData($data);
                            $model->save();
                        }
                    }
                }
            }
        }
    }
    
    /**
     *
     * @param type $productId
     * @return type
     */
    public function getMembershipProduct($productId)
    {
        $model = $this->_MembershipProductsFactory->create();
        $model->load($productId, 'product_id');
        return $model->getData();
    }
}
