<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Log;

class Edit extends \Wizkunde\WebSSO\Controller\Adminhtml\Log
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $logModel = $this->logFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id !== null) {
            $logModel->load($id);
        }

        $this->getCoreRegistry()->register('_sso_log', $logModel);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->setActiveMenu('Wizkunde_WebSSO::server');
        $resultPage->getConfig()->getTitle()->prepend(__('Audit Log'));

        return $resultPage;
    }
}
