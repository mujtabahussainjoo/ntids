<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use MageWorx\ShippingRules\Api\ExtendedZoneInterface;
use MageWorx\ShippingRules\Api\Data\ExtendedZoneDataInterface;

/**
 * Class ExtendedZone
 *
 * @method \MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone _getResource()
 * @method \MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone getResource()
 *
 */
class ExtendedZone extends AbstractModel implements ExtendedZoneInterface, ExtendedZoneDataInterface
{
    const EXTENDED_ZONE_TABLE_NAME = 'mageworx_shippingrules_extended_zone';
    const EXTENDED_ZONE_STORE_TABLE_NAME = 'mageworx_shippingrules_extended_zone_store';
    const EXTENDED_ZONE_LABELS_TABLE_NAME = 'mageworx_shippingrules_extended_zone_labels';

    const REGISTRY_KEY = 'extended_zone';

    /**
     * @var \MageWorx\ShippingRules\Helper\Data
     */
    protected $helper;

    /**
     * @var array
     */
    protected $storeLabels;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageWorx\ShippingRules\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->helper = $helper;
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone');
        $this->setIdFieldName('entity_id');
    }

    /**
     * Get url to the image, if exist
     *
     * @return string
     */
    public function getImageUrl()
    {
        $imagePath = $this->getImage();
        if (!$imagePath) {
            return '';
        }

        return $this->helper->getMediaUrl($imagePath);
    }

    /**
     * Return priority of the current zone (sort order)
     *
     * @return int
     */
    public function getPriority()
    {
        return (int)$this->getData('priority');
    }

    /**
     * @return boolean
     */
    public function getIsActive()
    {
        return (bool)$this->getData('is_active');
    }

    /**
     * Unique zone name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Get label for store
     *
     * @param null $storeId
     * @return string
     */
    public function getLabel($storeId = null)
    {
        $labels = $this->getStoreLabels();
        if ($storeId != null && !empty($labels[$storeId])) {
            return $labels[$storeId];
        }

        return $this->getName();
    }

    /**
     * Zone description text
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData('description');
    }

    /**
     * Image path (relative). Used as zone preview on frontend
     *
     * @return string
     */
    public function getImage()
    {
        return $this->getData('image');
    }

    /**
     * List of countries assigned to the zone
     *
     * @return array
     */
    public function getCountriesId()
    {
        $countries = $this->getData('countries_id');
        if (is_string($countries)) {
            $countries = explode(',', $countries);
            $this->setData('countries_id', $countries);
        }

        return $countries;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * Get corresponding store labels
     * where the key is store view id (int), value is label (string)
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (empty($this->storeLabels) && !empty($this->getData('store_labels'))) {
            $this->storeLabels = $this->getData('store_labels');
        } elseif (empty($this->storeLabels)) {
            $this->storeLabels = $this->getResource()->getStoreLabels($this->getId());
        }

        return $this->storeLabels;
    }
}
