<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use Magento\Backend\App\Action\Context;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneParentController;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save extends ExtendedZoneParentController
{
    /**
     * @var Factory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param Factory $dataObjectFactory
     * @param ExtendedZoneRepositoryInterface $zoneRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Factory $dataObjectFactory,
        ExtendedZoneRepositoryInterface $zoneRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context, $coreRegistry, $zoneRepository, $logger);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Pop-up Zone save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('mageworx_shippingrules/*/');
        }

        try {
            $id = $this->getRequest()->getParam('entity_id');
            if ($id) {
                /** @var $model \MageWorx\ShippingRules\Model\ExtendedZone */
                $model = $this->zoneRepository->getById($id);
            } else {
                /** @var $model \MageWorx\ShippingRules\Model\ExtendedZone */
                $model = $this->zoneRepository->getEmptyEntity();
            }
            $this->_eventManager->dispatch(
                'adminhtml_controller_shippingrules_extended_zone_prepare_save',
                ['request' => $this->getRequest()]
            );

            $data = $this->getRequest()->getPostValue();
            $data = $this->prepareData($data);
            $model->addData($data);
            $this->_session->setPageData($model->getData());

            $this->zoneRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the zone.'));
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
            $id = (int)$this->getRequest()->getParam('entity_id');
            if (!empty($id)) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('mageworx_shippingrules/*/new');
            }

            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the zone data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);

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
        if (array_search(Store::DEFAULT_STORE_ID, $data['store_id']) !== false) {
            $data['store_id'] = [Store::DEFAULT_STORE_ID];
        }

        if (!empty($data['image'][0]['file'])) {
            $data['image'] = $data['image'][0]['file'];
        } elseif (!empty($data['image'][0]['path'])) {
            $data['image'] = $data['image'][0]['path'];
        } else {
            $data['image'] = '';
        }

        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
