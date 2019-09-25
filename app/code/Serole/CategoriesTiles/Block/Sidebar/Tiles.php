<?php


namespace Serole\CategoriesTiles\Block\Sidebar;

class Tiles extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
	protected $_cart;
    protected $_checkoutSession; 
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
		$this->_cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function Tilesidebar()
    {
        //Your block code
        return __('Hello Developer! This how to get the storename: %1 and this is the way to build a url: %2', $this->_storeManager->getStore()->getName(), $this->getUrl('contacts'));
    }
	public function CartDetails(){
		$objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
		$cartData = $objectManager->create('Magento\Checkout\Model\Cart')->getQuote()->getAllVisibleItems();
		return $cartData;
	}
	public function getCart() { 
        return $this->_cart;
    }
  
    public function getCheckoutSession() {
        return $this->_checkoutSession;
    }
}
