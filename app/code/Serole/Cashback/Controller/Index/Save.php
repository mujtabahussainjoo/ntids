<?php
namespace Serole\Cashback\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $_customerSession;

   /**
     * @param Action\Context $context
     */
    public function __construct(
	            Context $context, 
	            Session $customerSession
            )
    {
		$this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
		$custId = $this->_customerSession->getId();
		if(isset($custId) && $custId != '')
		  $data['customer_id'] = $this->_customerSession->getId();
	    else
		   {
			   $this->messageManager->addError(__('Customer is not logged in.'));
			   return $resultRedirect->setPath('*/*/register');
		   }
	  
	      $data['verified'] = 1;
	      $data['status'] = 1;
		
        if (isset($data) && !empty($data) && $data['card_type'] != ''  && $data['owner_name'] != ''  && $data['cvv_no'] != ''  && $data['issuing_bank'] != '') {
			
            $model = $this->_objectManager->create('Serole\Cashback\Model\Carddetails');
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Card Details has been saved.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Card details.'));
            }
 
            return $resultRedirect->setPath('*/*/register');
        }
		else
		{
			$this->messageManager->addError(__('Kindly fill the complete form.'));
            return $resultRedirect->setPath('*/*/register');
		}
        return $resultRedirect->setPath('*/*/');
    }
}