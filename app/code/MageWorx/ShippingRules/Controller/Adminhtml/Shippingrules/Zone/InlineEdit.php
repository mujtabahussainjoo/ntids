<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Zone;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\ZoneRepositoryInterface as ZoneRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\ZoneInterface;
use MageWorx\ShippingRules\Model\Zone;

class InlineEdit extends Action
{
    /** @var ZoneRepository  */
    protected $zoneRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param ZoneRepository $zoneRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        ZoneRepository $zoneRepository,
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
            /** @var Zone $zone */
            $zone = $this->zoneRepository->getById($zoneId);
            try {
                $zoneData = $postItems[$zoneId];
                $extendedZoneData = $zone->getData();
                $this->setZoneData($zone, $extendedZoneData, $zoneData);
                $this->zoneRepository->save($zone);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithZoneId($zone, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithZoneId($zone, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithZoneId(
                    $zone,
                    __('Something went wrong while saving the location group.')
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
     * @param ZoneInterface $zone
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithZoneId(ZoneInterface $zone, $errorText)
    {
        return '[Location Group ID: ' . $zone->getEntityId() . '] ' . $errorText;
    }

    /**
     * Set zone data
     *
     * @param Zone $zone
     * @param array $extendedZoneData
     * @param array $zoneData
     * @return $this
     */
    public function setZoneData(Zone $zone, array $extendedZoneData, array $zoneData)
    {
        $zone->setData(array_merge($zone->getData(), $extendedZoneData, $zoneData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::zone');
    }
}
