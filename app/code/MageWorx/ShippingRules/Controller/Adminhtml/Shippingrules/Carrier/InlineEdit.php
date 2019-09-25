<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Carrier;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\CarrierRepositoryInterface as CarrierRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\CarrierInterface;
use MageWorx\ShippingRules\Model\Carrier;

class InlineEdit extends Action
{
    /** @var CarrierRepository  */
    protected $carrierRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param CarrierRepository $carrierRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        CarrierRepository $carrierRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->carrierRepository = $carrierRepository;
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

        foreach (array_keys($postItems) as $carrierId) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->getById($carrierId);
            try {
                $carrierData = $postItems[$carrierId];
                $extendedCarrierData = $carrier->getData();
                $this->setCarrierData($carrier, $extendedCarrierData, $carrierData);
                $this->carrierRepository->save($carrier);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithCarrierId($carrier, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCarrierId($carrier, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCarrierId(
                    $carrier,
                    __('Something went wrong while saving the carrier.')
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
     * Add carrier id to error message
     *
     * @param CarrierInterface $carrier
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithCarrierId(CarrierInterface $carrier, $errorText)
    {
        return '[Carrier ID: ' . $carrier->getCarrierId() . '] ' . $errorText;
    }

    /**
     * Set carrier data
     *
     * @param Carrier $carrier
     * @param array $extendedCarrierData
     * @param array $carrierData
     * @return $this
     */
    public function setCarrierData(Carrier $carrier, array $extendedCarrierData, array $carrierData)
    {
        $carrier->setData(array_merge($carrier->getData(), $extendedCarrierData, $carrierData));

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
