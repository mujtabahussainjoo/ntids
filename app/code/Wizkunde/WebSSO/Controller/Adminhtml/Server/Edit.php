<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Server;

class Edit extends \Wizkunde\WebSSO\Controller\Adminhtml\Server
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $serverModel = $this->serverModelFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id !== null) {
            $serverModel->load($id);
        }
        
        $this->getCoreRegistry()->register('_sso_server', $serverModel);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->setActiveMenu('Wizkunde_WebSSO::server');
        $resultPage->getConfig()->getTitle()->prepend(__('Server'));

        return $resultPage;
    }
}
