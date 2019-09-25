<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\RateRepositoryInterface as RateRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\RateInterface;
use MageWorx\ShippingRules\Model\Carrier\Method\Rate;

class InlineEdit extends Action
{
    /** @var RateRepository  */
    protected $rateRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param RateRepository $rateRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        RateRepository $rateRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->rateRepository = $rateRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $rateId) {
            /** @var Rate $rate */
            $rate = $this->rateRepository->getById($rateId);
            try {
                $rateData = $postItems[$rateId];
                $extendedRateData = $rate->getData();
                $this->setRateData($rate, $extendedRateData, $rateData);
                $this->rateRepository->save($rate);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithRateId($rate, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRateId($rate, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRateId(
                    $rate,
                    __('Something went wrong while saving the rate.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add rate id to error message
     *
     * @param RateInterface $rate
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRateId(RateInterface $rate, $errorText)
    {
        return '[Rate ID: ' . $rate->getRateId() . '] ' . $errorText;
    }

    /**
     * Set rate data
     *
     * @param Rate $rate
     * @param array $extendedRateData
     * @param array $rateData
     * @return $this
     */
    public function setRateData(Rate $rate, array $extendedRateData, array $rateData)
    {
        $rate->setData(array_merge($rate->getData(), $extendedRateData, $rateData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::carrier');
    }
}
