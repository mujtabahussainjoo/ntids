<?php


namespace Serole\Serialcode\Controller\Adminhtml\Codes;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Serole\OvernightUpload\Model\GridFactory
     */
    var $gridFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Serole\Serialcode\Model\OrderitemSerialcodeFactory $serialFactory
    ) {
        parent::__construct($context);
        $this->serialFactory = $serialFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('serial/code/addrow');
            return;
        }
        try {
            $rowData = $this->serialFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setEntityId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('serial/codes/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_Serialcode::save');
    }
}
