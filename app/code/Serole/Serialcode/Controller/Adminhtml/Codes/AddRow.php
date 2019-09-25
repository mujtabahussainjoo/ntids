<?php

namespace Serole\Serialcode\Controller\Adminhtml\Codes;

use Magento\Framework\Controller\ResultFactory;

class AddRow extends \Magento\Backend\App\Action
{

    private $coreRegistry;


    private $gridFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Serole\Serialcode\Model\OrderitemSerialcodeFactory $serialFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->serialFactory = $serialFactory;
    }


    public function execute(){
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->serialFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            //print_r($rowData->getData()); exit;
            $rowTitle = "Serial Code Data";
            if (!$rowData->getId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('serial/codes/index');
                return;
            }
        }

        $this->coreRegistry->register('serialcode_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Row Data ').$rowTitle : __('Add Row Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed(){
        return $this->_authorization->isAllowed('Serole_Serialcode::serialcode_row');
    }
}
