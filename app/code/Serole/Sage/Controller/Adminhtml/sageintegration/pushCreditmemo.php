<?php
namespace Serole\Sage\Controller\Adminhtml\sageintegration;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class pushCreditmemo extends \Magento\Backend\App\Action
{
	protected $_exportOrder;
	
	public function __construct(Action\Context $context,\Serole\Sage\Model\Exportorder $exportOrder) 
	{
		$this->_exportOrder = $exportOrder;
		parent::__construct($context);
	}		
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $itemIds = $this->getRequest()->getParam('sageintegration');
		
        if (!is_array($itemIds) || empty($itemIds)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($itemIds as $itemId) {
                    $post = $this->_objectManager->get('Serole\Sage\Model\Sageintegration')->load($itemId);
					$orderIncrementId = $post->getOrderid();
					$orderId = $this->_exportOrder->getOrderIdByIncrementId($orderIncrementId);
					$this->_exportOrder->pushSingleCM($orderId);
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been pushed.', count($itemIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sage/*/index');
    }
}