<?php

namespace Serole\MemberList\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
	protected $_customerSession;
	
	protected $_messageManager;
	
	public function __construct(
        \Magento\Customer\Model\Session $customerSession, 
		\Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    )
    {        
        $this->_customerSession = $customerSession;
		$this->_messageManager = $messageManager;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
		
        $cus = $observer->getEvent()->getCustomer();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();

		$customerId = $cus->getId();

		$customer = $customerFactory->load($customerId);

		if($customer->getIsSuspended())
		{
			$this->_messageManager->addError("Your account has been suspended");
			$this->_customerSession->logout();
		}
    }
}
