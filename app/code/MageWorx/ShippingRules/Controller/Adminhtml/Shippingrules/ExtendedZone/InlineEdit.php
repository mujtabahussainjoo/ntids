<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface as ExtendedZoneRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterface;
use MageWorx\ShippingRules\Model\ExtendedZone;

class InlineEdit extends Action
{
    /** @var ExtendedZoneRepository  */
    protected $zoneRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param ExtendedZoneRepository $zoneRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        ExtendedZoneRepository $zoneRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->zoneRepository = $zoneRepository;
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

        foreach (array_keys($postItems) as $zoneId) {
            /** @var ExtendedZone $zone */
            $zone = $this->zoneRepository->getById($zoneId);
            try {
                $zoneData = $postItems[$zoneId];
                $extendedExtendedZoneData = $zone->getData();
                $this->setExtendedZoneData($zone, $extendedExtendedZoneData, $zoneData);
                $this->zoneRepository->save($zone);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithExtendedZoneId($zone, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithExtendedZoneId($zone, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithExtendedZoneId(
                    $zone,
                    __('Something went wrong while saving the zone.')
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
     * Add zone id to error message
     *
     * @param ExtendedZoneDataInterface $zone
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithExtendedZoneId(ExtendedZoneDataInterface $zone, $errorText)
    {
        return '[Pop-up Zone ID: ' . $zone->getEntityId() . '] ' . $errorText;
    }

    /**
     * Set zone data
     *
     * @param ExtendedZone $zone
     * @param array $extendedExtendedZoneData
     * @param array $zoneData
     * @return $this
     */
    public function setExtendedZoneData(ExtendedZone $zone, array $extendedExtendedZoneData, array $zoneData)
    {
        $zone->setData(array_merge($zone->getData(), $extendedExtendedZoneData, $zoneData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::extended_zones');
    }
}
