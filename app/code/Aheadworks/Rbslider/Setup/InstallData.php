<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Aheadworks\Rbslider\Api\Data\BannerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Rbslider\Model\Sample;

/**
 * class InstallData
 * @package Aheadworks\Rbslider\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var BannerInterfaceFactory
     */
    private $bannerDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Sample
     */
    private $sampleData;

    /**
     * @param BannerRepositoryInterface $bannerRepository
     * @param BannerInterfaceFactory $bannerDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Sample $sampleData
     */
    public function __construct(
        BannerRepositoryInterface $bannerRepository,
        BannerInterfaceFactory $bannerDataFactory,
        DataObjectHelper $dataObjectHelper,
        Sample $sampleData
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->bannerDataFactory = $bannerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->sampleData = $sampleData;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        foreach ($this->sampleData->get() as $data) {
            try {
                $bannerDataObject = $this->bannerDataFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $bannerDataObject,
                    $data,
                    BannerInterface::class
                );

                if (!$bannerDataObject->getId()) {
                    $bannerDataObject->setId(null);
                }

                $this->bannerRepository->save($bannerDataObject);
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
}
