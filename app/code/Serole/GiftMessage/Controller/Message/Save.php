<?php
namespace Serole\GiftMessage\Controller\Message;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession)
    {
        $this->customerSession =  $customerSession;
        return parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->getRequest()->getParams();
        if($postData){
           $this->customerSession->setToName($postData['toadddress']);
           $this->customerSession->setFromName($postData['fromaddress']);
           $this->customerSession->setGiftMessage($postData['message']);
           $this->customerSession->setGiftEmail($postData['email']);
           $this->customerSession->setGiftImage($postData['image']);
           $this->messageManager->addSuccess(__('Gift Message Saved'));
        }else{
            $this->messageManager->addError("Something went wrong");
        }
        $this->_redirect('checkout/');
    }
}