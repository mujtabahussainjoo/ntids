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
	protected $_coreRegistry;


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
		\Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory
        CustomerCart $cart
    ) {
        $this->checkoutSession = $checkoutSession;
		$this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $pageFactory;
        $this->cart = $cart;

        parent::__construct($context);
    }

    public function execute()
    {
		$objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
		$cartData = $objectManager->create('Magento\Checkout\Model\Session')->getQuote()->getAllVisibleItems();
		$cartDataCount = count( $cartData );
		$this->_coreRegistry->register('cartData', $cartData);
        return $this->_resultPageFactory->create();
		
		
        // $allItems = $this->checkoutSession->getQuote()->getAllVisibleItems();
		// $post = $this->getRequest()->getPostValue();
		// echo "<pre>";
		// //print_r($post);
		// // print_r($post['itemId']);
		// // exit;
        // foreach ($allItems as $item) {
            // //$itemId = $item->getItemId();
            // $itemId = $post['itemId'];
            // $this->cart->removeItem($itemId)->save();
        // }

        // $message = __(
            // 'You deleted all item from shopping cart.'
        // );
        // $this->messageManager->addSuccessMessage($message);

        // $response = [
            // 'success' => true,
        // ];

        // $this->getResponse()->representJson(
            // $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
        // );
    }
}