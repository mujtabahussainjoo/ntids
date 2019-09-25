<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\ShippingRules\Api\RegionRepositoryInterface as RegionRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use MageWorx\ShippingRules\Api\Data\RegionInterface;
use MageWorx\ShippingRules\Model\Region;

class InlineEdit extends Action
{
    /** @var RegionRepository  */
    protected $regionRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param RegionRepository $regionRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        RegionRepository $regionRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->regionRepository = $regionRepository;
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

        foreach (array_keys($postItems) as $regionId) {
            /** @var Region $region */
            $region = $this->regionRepository->getById($regionId);
            try {
                $regionData = $postItems[$regionId];
                $extendedRegionData = $region->getData();
                $this->setRegionData($region, $extendedRegionData, $regionData);
                $this->regionRepository->save($region);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithRegionId($region, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRegionId($region, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRegionId(
                    $region,
                    __('Something went wrong while saving the region.')
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
     * Add region id to error message
     *
     * @param RegionInterface $region
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRegionId(RegionInterface $region, $errorText)
    {
        return '[Region ID: ' . $region->getRegionId() . '] ' . $errorText;
    }

    /**
     * Set region data
     *
     * @param Region $region
     * @param array $extendedRegionData
     * @param array $regionData
     * @return $this
     */
    public function setRegionData(Region $region, array $extendedRegionData, array $regionData)
    {
        $region->setData(array_merge($region->getData(), $extendedRegionData, $regionData));

        return $this;
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageWorx_ShippingRules::region');
    }
}
