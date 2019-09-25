<?php
namespace Serole\Cashback\Controller\Customer;

use Magento\Framework\App\Action\Context;

class Action extends \Magento\Framework\App\Action\Action
{
	protected $_carddetails;
	
	public function __construct(
	            Context $context,
	            \Serole\Cashback\Model\Carddetails $Carddetails
								)
    {
		$this->_carddetails = $Carddetails;
        parent::__construct($context);
    }
    public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
		$id = $_GET['id'];
		$card = $this->_carddetails->load($id);
		if($_GET['action'] == "Activate")
		{
			$this->messageManager->addSuccess(__('Card has been Activated.'));
			$card->setStatus(1);
		}
		else
		{
			$this->messageManager->addSuccess(__('Card has been Deactivated.'));
			$card->setStatus(0);
		}
		$card->save();
		return $resultRedirect->setPath('*/*/cards');
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}