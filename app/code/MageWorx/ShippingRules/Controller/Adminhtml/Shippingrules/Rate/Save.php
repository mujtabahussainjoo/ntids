<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use Magento\Backend\App\Action\Context;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use MageWorx\ShippingRules\Api\RateRepositoryInterface;
use MageWorx\ShippingRules\Api\MethodRepositoryInterface;
use Psr\Log\LoggerInterface;

class Save extends \MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Directory\Api\CountryInformationAcquirerInterface
     */
    protected $countryInformationAcquirer;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param FileFactory $fileFactory
     * @param Date $dateFilter
     * @param RateRepositoryInterface $rateRepository
     * @param MethodRepositoryInterface $methodRepository
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param LoggerInterface $logger
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        FileFactory $fileFactory,
        Date $dateFilter,
        RateRepositoryInterface $rateRepository,
        MethodRepositoryInterface $methodRepository,
        RegionFactory $regionFactory,
        DataObjectFactory $dataObjectFactory,
        LoggerInterface $logger,
        \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer
    ) {
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $dateFilter,
            $rateRepository,
            $methodRepository,
            $logger
        );
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
    }

    /**
     * Method save action
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('mageworx_shippingrules/*/');
        }

        try {
            $id = $this->getRequest()->getParam('rate_id');
            if ($id) {
                /** @var $model \MageWorx\ShippingRules\Model\Carrier\Method\Rate */
                $model = $this->rateRepository->getById($id);
            } else {
                /** @var $model \MageWorx\ShippingRules\Model\Carrier\Method\Rate */
                $model = $this->rateRepository->getEmptyEntity();
            }

            $this->_eventManager->dispatch(
                'adminhtml_controller_mageworx_shippingrules_rate_prepare_save',
                ['request' => $this->getRequest()]
            );
            $data = $this->getRequest()->getPostValue();
            $data = $this->prepareData($data);
            $validateResult = $model->validateData($this->dataObjectFactory->create(['data' => $data]));
            if ($validateResult !== true) {
                foreach ($validateResult as $errorMessage) {
                    $this->messageManager->addErrorMessage($errorMessage);
                }
                $this->_session->setPageData($data);
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $model->getData('rate_id')]);

                return;
            }

            $model->addData($data);
            $this->_session->setPageData($model->getData());
            $this->rateRepository->save($model);
            $this->messageManager->addSuccessMessage(__('You saved the rate.'));
            $this->_session->setPageData(false);

            if ($this->getRequest()->getParam('back') == 'newAction') {
                $this->_redirect('mageworx_shippingrules/*/newAction');

                return;
            }
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $model->getData('rate_id')]);

                return;
            }
            if ($this->isBackToMethod($data)) {
                $this->_redirect(
                    'mageworx_shippingrules/shippingrules_method/edit',
                    ['id' => $model->getData('method_id')]
                );

                return;
            }
            $this->_redirect('mageworx_shippingrules/*/');

            return;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('rate_id');
            if (!empty($id)) {
                $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $id]);
            } else {
                $this->_redirect('mageworx_shippingrules/*/new');
            }

            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the rate data. Please review the error log.')
            );
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('mageworx_shippingrules/*/edit', ['id' => $this->getRequest()->getParam('rate_id')]);

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
        if (!isset($data['rate_id']) || !$data['rate_id']) {
            $data['rate_id'] = null;
        }

        if (empty($data['region_id'])) {
            $data['region_id'] = [];
        }

        if (empty($data['country_id'])) {
            $data['country_id'] = [];
        }

        unset($data['created_at']);
        unset($data['updated_at']);

        return $data;
    }
}
