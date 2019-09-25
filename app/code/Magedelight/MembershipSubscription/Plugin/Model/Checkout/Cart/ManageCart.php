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

namespace Magedelight\MembershipSubscription\Plugin\Model\Checkout\Cart;

class ManageCart
{
     /**
      * @var \Magento\Quote\Model\Quote
      */
    protected $quote;
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;

    /**
     *
     * @var ManagerInterface
     */
    protected $_messageManager;
    
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->_messageManager = $messageManager;
        $this->quote = $checkoutSession->getQuote();
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
    }

    /**
     *
     * @param \Magento\Checkout\Model\Cart $subject
     * @param int $data
     * @return type
     */
    public function beforeupdateItems(\Magento\Checkout\Model\Cart $subject, $data)
    {
        $durationOption = $this->request->getParam('duration_option');
        
        if (!isset($durationOption)) {
            foreach ($data as $key => $value) {
                if ($key) {
                    $quote = $subject->getQuote();
                    $item = $quote->getItemById($key);
                    $productType = $item->getProductType();

                    if ($productType == "Membership") {
                        $data[$key]['qty'] = 1;
                        if ($item->getQty() > 0) {
                            $this->_messageManager->addNoticeMessage('You cannot add more than one membership plan to cart.');
                        }
                    }
                }
            }
        }
        
        return [$data];
    }
    
    
    /**
     * beforeAddProduct
     *
     * @param      $subject
     * @param      $productInfo
     * @param null $requestInfo
     *
     * @return array
     * @throws LocalizedException
     */
    public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
    {
        if ($productInfo->getTypeId() == "Membership") {
            $allItems =  $this->quote->getAllVisibleItems();
            
            $deleteItem = [];
            foreach ($allItems as $item) {
                $membershipProducts = $this->getMembershipProducts();
                $productId = $item->getProductId();

                if (in_array($productId, $membershipProducts)) {
                    $deleteItem[] = $item->getItemId();
                }
            }

            if (count($deleteItem) > 0) {
                return $this->_messageManager->addError(__('You cannot add more than one membership plan to cart.'));
            }
        }
    }
    
    
    /**
     * get membership product
     * @return Array
     */
    public function getMembershipProducts()
    {
        $model = $this->_MembershipProductsFactory->create()->getCollection();
        $modelData = $model->getData();
        foreach ($modelData as $key => $value) {
            $membershipProducts[] = $value['product_id'];
        }
        
        return $membershipProducts;
    }
    
    /**
     * create item model
     */
    public function getItemModel()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');
        return $itemModel;
    }
}
