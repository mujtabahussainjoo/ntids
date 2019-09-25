<?php
namespace Serole\SideBarCart\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	
	protected $cartSession;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Checkout\Model\Session $cartSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		$this->cartSession = $cartSession;
		return parent::__construct($context);
	}

	public function execute()
	{   
	      $items = $this->cartSession->getQuote()->getAllVisibleItems();
          $resultPage = $this->_pageFactory->create();
	      $block = $resultPage->getLayout()
                ->createBlock('Serole\SideBarCart\Block\Index\Index')
                ->setTemplate('Serole_SideBarCart::ajax.phtml')
				->setData('data',$items)
                ->toHtml();
          $this->getResponse()->setBody($block);
	}
}
