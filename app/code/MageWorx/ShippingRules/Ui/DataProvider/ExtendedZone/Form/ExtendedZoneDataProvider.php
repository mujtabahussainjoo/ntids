<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\ExtendedZone\Form;

use MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use MageWorx\ShippingRules\Helper\Data as Helper;

class ExtendedZoneDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param StoreManagerInterface $storeManager
     * @param Helper $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        Helper $helper,
        array $meta = [],
        array $data = []
    ) {
    
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->collection = $collectionFactory->create();
        $this->helper = $helper;
    }


    /**
     * Get meta from all modifiers
     *
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $modifiers = $this->pool->getModifiersInstances();
        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * Get data from all modifiers and items from the collection
     *
     * @return array
     */
    public function getData()
    {
        if (!empty($this->data)) {
            return $this->data;
        }
        $items = $this->collection->getItems();
        $isSingleStore = $this->storeManager->isSingleStoreMode();
        /** @var \MageWorx\ShippingRules\Model\ExtendedZone $zone */
        foreach ($items as $zone) {
            $this->data[$zone->getId()] = $zone->getData();
            $this->data[$zone->getId()]['single_store'] = $isSingleStore;
            $image = $zone->getImage();
            if ($image) {
                $imagePathParts = explode('/', $image);
                $imageName = array_pop($imagePathParts);
                $imageData = [
                    'name' => $imageName,
                    'url' => $this->helper->getImageUrl($image, Helper::IMAGE_TYPE_FORM_PREVIEW),
                    'path' => $image,
                    'size' => $this->helper->getImageOrigSize($image)
                ];
                $this->data[$zone->getId()]['image'] = [$imageData];
            }
        }

        $modifiers = $this->pool->getModifiersInstances();
        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }
}
