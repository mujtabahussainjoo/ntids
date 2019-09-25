<?php

namespace Serole\ExternalProduct\Controller\Index;

use Magento\Framework\Controller\ResultFactory;


class Index extends \Magento\Framework\App\Action\Action
{
	protected $_productloader;
	
	protected $_referral;
	
	protected $_storeManager;
	
	protected $_customerSession;
	
	protected $_messageManager;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
		\Magento\Customer\Model\Session $customerSession,
		\Serole\ExternalProduct\Model\ReferralFactory $referralFactory

    ) {
        $this->_productloader = $_productloader;
		$this->_referral = $referralFactory;
		$this->_storeManager = $storeManagerInterface;
		$this->_customerSession = $customerSession;
        parent::__construct($context);
      } 
	
	
    public function execute()
    {
		$productId = $this->getRequest()->getParams('productId');
		
		$product = $this->getLoadProduct($productId);
		
		$storeCode = $this->_storeManager->getStore()->getCode();
		
		$customerId = $this->_customerSession->getCustomer()->getId();
		
		$linkUrl = $product->getData('referral_link_url');
		
		if($linkUrl != '') {
			$status = 'complete';
		} else {
			$status = 'error';
		}
		
		$referral = $this->_referral->create();
		$referral->setCustomerid($customerId);
		$referral->setProductid($productId['productId']);
		$referral->setLinkurl($linkUrl);
		$referral->setStore($storeCode);
		$referral->setStatus($status);
		$referral->save();
		
		if($linkUrl != '') {
			header('Location:'.$linkUrl);
			exit();
		} else {
			$this->messageManager->addError("Offer Link unable to find.");
			$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
		}

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
	
	 public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
}