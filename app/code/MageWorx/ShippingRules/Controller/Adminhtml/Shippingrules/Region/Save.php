<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region;

use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use MageWorx\ShippingRules\Api\RegionRepositoryInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region as RegionParentController;
use Psr\Log\LoggerInterface;

class Save extends RegionParentController
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param RegionRepositoryInterface $regionRepository
     * @param LoggerInterface $logger
     * @param DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        RegionRepositoryInterface $regionRepository,
        LoggerInterface $logger,
        DataObjectFactory $dataObjectFactory
    ) {

        parent::__construct($context, $coreRegistry, $regionRepository, $logger);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Region save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('mageworx_shippingrules/*/');
        }

        try {
            $id = (int)$this->getRequest()->getParam('region_id');
            if ($id) {
                /** @var $model \MageWorx\ShippingRules\Model\Region */
                $model = $this->regionRepository->getById($id);
            } else {
                /** @var $model \MageWorx\ShippingRules\Model\Region */
                $model = $this->regionRepository->getEmptyEntity();
            }
            $this->_eventManager->dispatch(
                'adminhtml_controller_mageworx_shippingrules_region_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue();
            $data = $this->prepareData($data);

            $validateResult = $model->validateData($this->dataObjectFactory->create(['data' => $data]));
            if (!empty($validateResult)) {
                foreach ($validateResult as $errorMessage) {
                    $this->messageManager->addErrorMessage($errorMessage);
                }
                $this->_session->setPageData($data);
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $model->getData('region_id')]);

                return;
            }

            $model->addData($data);
            $this->_session->setPageData($model->getData());
            $this->regionRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the region.'));
            $this->_session->setPageData(false);
            if ($this->getRequest()->getParam('back') == 'newAction') {
                $this->_redirect('mageworx_shippingrules/*/newAction');
                return;
            }
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $model->getId()]);
                return;
            }
            $this->_redirect('mageworx_shippingrules/*/');

            return;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('region_id');
            if (!empty($id)) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('mageworx_shippingrules/*/new');
            }

            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the region data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('region_id')]);

            return;
        }
    }

    /**
     * Prepares specific data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        if (!isset($data['region_id']) || !$data['region_id']) {
            $data['region_id'] = null;
        }

        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
