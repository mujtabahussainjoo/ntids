<?php


namespace Serole\OvernightUpload\Controller\Adminhtml\Provider;

class Save extends \Magento\Backend\App\Action
{

    var $gridFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Serole\OvernightUpload\Model\ProvidergridFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->gridFactory = $gridFactory;
    }


    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        //echo "<pre>"; print_r($data); exit;
        if (!$data) {
            $this->_redirect('grid/provider/addrow');
            return;
        }
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $providerObj = $objectManager->create('\Serole\OvernightUpload\Model\Providergrid')->getCollection();
            $providerObj->addFieldToFilter('providerid',$data['providerid']);
            $providerItem = $providerObj->getFirstItem();
            if(isset($data['id'])){
                if($data['id']  == $providerItem['id']){
                    $rowData = $this->gridFactory->create();
                    $rowData->setData($data);
                    if (isset($data['id'])) {
                        $rowData->setId($data['id']);
                    }
                    $rowData->save();
                    $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
                }else{
                    $this->messageManager->addWarning($data['providerid']."  Provider already exists");
                }

            }else{
                if(!$providerItem->getData()) {
                    $rowData = $this->gridFactory->create();
                    $rowData->setData($data);
                    if (isset($data['id'])) {
                        $rowData->setId($data['id']);
                    }
                    $rowData->save();
                    $this->messageManager->addSuccess(__('Row data has been successfully saved.'));
                }else{
                    $this->messageManager->addWarning($data['providerid']."  Provider already exists");
                }
            }

        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('grid/provider/index');
    }


    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_OvernightUpload::save');
    }
}
