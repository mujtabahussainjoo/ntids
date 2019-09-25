<?php

namespace Serole\Cashback\Block\Customer;


class Cards extends \Magento\Framework\View\Element\Template {
	
	protected $_carddetailsFactory;
	
	protected $_customerSession;

    public function __construct(
	          \Magento\Catalog\Block\Product\Context $context,
			  \Serole\Cashback\Model\CarddetailsFactory $CarddetailsFactory,
			  \Magento\Customer\Model\Session $customerSession,
	          array $data = []
			  ) 
	{
        $this->_carddetailsFactory = $CarddetailsFactory;
		$this->_customerSession = $customerSession;
        parent::__construct($context, $data);

    }
	
	public function getAllCards()
	{
		$om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->create('Magento\Customer\Model\Session');
		$customer_id = $customerSession->getCustomer()->getId();
		
		return $collection = $this->_carddetailsFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id));
							
	}


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}