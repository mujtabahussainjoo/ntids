<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneParentController;
use MageWorx\ShippingRules\Api\ExtendedZoneRepositoryInterface;
use MageWorx\ShippingRules\Helper\Data;
use Psr\Log\LoggerInterface;

class ImageUpload extends ExtendedZoneParentController
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * ImageUpload constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param ExtendedZoneRepositoryInterface $zoneRepository
     * @param LoggerInterface $logger
     * @param StoreManagerInterface $storeManager
     * @param RawFactory $resultRawFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ExtendedZoneRepositoryInterface $zoneRepository,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        RawFactory $resultRawFactory,
        Data $helper
    ) {
        parent::__construct($context, $coreRegistry, $zoneRepository, $logger);
        $this->storeManager = $storeManager;
        $this->resultRawFactory = $resultRawFactory;
        $this->helper = $helper;
    }

    /**
     * Upload image action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            /** @var Uploader $uploader */
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'svg']);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
            $result = $uploader->save($mediaDirectory->getAbsolutePath($this->helper->getBaseMediaPath()));
            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $this->helper->getMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));

        return $response;
    }
}
