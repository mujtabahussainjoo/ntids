<?php

namespace Serole\Subscriber\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

 
	public function Subscribe() {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$conf = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/status',$storeScope);
       	$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		$customerIdContrl = $customerSession->getId();
		$isSubscribe=$this->getRequest()->getParam('status');
		
		if($conf==1 && $isSubscribe==1){ 
			$customerSession->setCustomerIdController($customerIdContrl);
		}
	}	
    public function execute()
    {
		$this->Subscribe();
        // $this->_view->loadLayout();
        // $this->_view->getLayout()->initMessages();
        // $this->_view->renderLayout();
    }
}