<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml\Server;

class Delete extends \Wizkunde\WebSSO\Controller\Adminhtml\Server
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id !== null) {
            $serverModel = $this->serverModelFactory->create()->load($id);

            // Check this news exists or not
            if (!$serverModel->getId()) {
                $this->messageManager->addError(__('This server no longer exists.'));
            } else {
                try {
                    // Delete news
                    $serverModel->delete();
                    $this->messageManager->addSuccess(__('The server has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $serverModel->getId()]);
                }
            }
        }
    }
}
