<?php

namespace Serole\SideBarCart\Controller\Index;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Cart as CustomerCart;
class Remove extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param CustomerCart $cart
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        CustomerCart $cart
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;

        parent::__construct($context);
    }

    public function execute()
    {
        $allItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
		$post = $this->getRequest()->getParam('itemId');
		// echo "<pre>";
		// print_r($post);
		// exit;
        foreach ($allItems as $item) {
            //$itemId = $item->getItemId();
            $itemId = $post;
            $this->cart->removeItem($itemId)->save();
        }

		$this->messageManager->addSuccessMessage('You deleted item from shopping cart.');
		$resultRedirect = $this->resultRedirectFactory->create();
		$resultRedirect->setRefererOrBaseUrl();
		return $resultRedirect;
    }
}