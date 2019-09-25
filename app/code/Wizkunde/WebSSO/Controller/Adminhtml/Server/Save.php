<?php
namespace Wizkunde\WebSSO\Controller\Adminhtml\Server;

use Wizkunde\WebSSO\Controller\Adminhtml\Server;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends Server
{
    /**
     * Save Server
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/server'));
        }

        $serverModel = $this->serverModelFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            $serverModel->load($id);
        }

        try {
            $data = $request->getParams();
            unset($data['id']);

            $serverModel->addData($data);
            $serverModel->save();

            if ($serverModel->getId()) {
                if ($data['connection_type'] == 'OAuth2') {
                    $typeModel = $this->oauth2ModelFactory->create();
                } else {
                    $typeModel = $this->saml2ModelFactory->create();
                }

                $typeModel->load($serverModel->getId(), 'server_id');

                $typeModel->setServerId($serverModel->getId());
                $typeModel->addData($data);

                $typeModel->save();

                // Save the mapping information
                if (isset($data['mapping']) && is_array($data['mapping'])) {
                    $mappingCollection = $this->mappingCollectionFactory->create();
                    
                    // Remove old mappings
                    $mappingCollection->addFieldToFilter('server_id', $serverModel->getId());
                    $mappingCollection->walk('delete');

                    foreach ($data['mapping']['value'] as $option => $valueData) {
                        $mappingModel = $this->mappingModelFactory->create();
                        
                        $mappingModel->setData($valueData);

                        $externalData = [
                            'value' => $valueData['external'],
                            'transform' => $valueData['transform'],
                            'extra' => $valueData['extra']
                        ];

                        $mappingModel->setExternal(serialize($externalData));
                        $mappingModel->setServerId($serverModel->getId());
                        $mappingModel->save();
                    }
                }
            }

            $this->messageManager->addSuccess(__('The server has been saved.'));
            $this->_getSession()->setFormData(false);
        } catch (LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('websso_server_form_data', $this->getRequest()->getParams());
            $this->getResponse()->setRedirect(
                $this->getUrl('*/*/edit', ['id' => $serverModel->getId(), '_current' => true])
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this server.'));
            $this->_getSession()->setData('websso_server_form_data', $this->getRequest()->getParams());
            $this->getResponse()->setRedirect(
                $this->getUrl('*/*/edit', ['id' => $serverModel->getId(), '_current' => true])
            );
        }

        $this->getResponse()->setRedirect($this->getUrl('*/*/'));
    }
}
