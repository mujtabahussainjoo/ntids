<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier as CarrierParentController;
use Psr\Log\LoggerInterface;

class Save extends CarrierParentController
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param CarrierRepositoryInterface $carrierRepository
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        CarrierRepositoryInterface $carrierRepository,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger
    ) {
    

        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $carrierRepository, $logger);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Carrier save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('mageworx_shippingrules/*/');
        }

        try {
            $id = (int)$this->getRequest()->getParam('id');
            if (!$id && $this->getRequest()->getParam('carrier_id')) {
                $id = (int)$this->getRequest()->getParam('carrier_id');
            }
            if ($id) {
                /** @var $model \MageWorx\ShippingRules\Model\Carrier */
                $model = $this->carrierRepository->getById($id);
            } else {
                /** @var $model \MageWorx\ShippingRules\Model\Carrier */
                $model = $this->carrierRepository->getEmptyEntity();
            }
            $this->_eventManager->dispatch(
                'adminhtml_controller_mageworx_shippingrules_carrier_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue('carrier');
            $data['store_labels'] = $this->getRequest()->getPostValue('store_labels');
            $data = $this->prepareData($data);
            $validateResult = $model->validateData($this->dataObjectFactory->create(['data' => $data]));
            if ($validateResult !== true) {
                foreach ($validateResult as $errorMessage) {
                    $this->messageManager->addErrorMessage($errorMessage);
                }
                $this->_session->setPageData($data);
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $model->getId()]);
                return;
            }

            $model->loadPost($data);
            $model->addData($data);

            $this->_session->setPageData($model->getData());

            $this->carrierRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the carrier.'));
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
            $id = (int)$this->getRequest()->getParam('id');
            if (!empty($id)) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('mageworx_shippingrules/*/new');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the carrier data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('id')]);
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
        if (empty($data['carrier_id'])) {
            $data['carrier_id'] = null;
        }

        if (empty($data['carrier_code']) && !empty($data['title'])) {
            $code = mb_strtolower($data['title']);
            $code = preg_replace('/[^\da-z]/i', '', $code);
            $code = 'code' . $code;
            $data['carrier_code'] = $code;
        }

        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
