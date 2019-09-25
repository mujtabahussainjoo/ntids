<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Rule\Model\AbstractModel;
use Magento\Rule\Model\Action\CollectionFactory;
use MageWorx\ShippingRules\Api\Data\ZoneInterface;
use MageWorx\ShippingRules\Model\ResourceModel\Zone as ZoneResource;
use MageWorx\ShippingRules\Model\ResourceModel\Zone\CollectionFactory as ZoneCollectionFactory;
use MageWorx\ShippingRules\Model\Zone\Condition\CombineFactory;

/**
 * Class Zone
 *
 * @method Zone setZoneId(int $id)
 * @method Zone setName(string $name)
 * @method Zone setTitle(string $title)
 * @method ZoneResource _getResource()
 * @method ZoneResource getResource()
 *
 */
class Zone extends AbstractModel implements ZoneInterface
{
    const CURRENT_ZONE = 'current_zone';
    const ZONE_TABLE_NAME = 'mageworx_shippingrules_zone';
    const ZONE_STORE_TABLE_NAME = 'mageworx_shippingrules_zone_store';

    /**
     * @var CombineFactory
     */
    protected $condCombineFactory;

    /**
     * @var CollectionFactory
     */
    protected $actionsCollectionFactory;

    /**
     * @var ResourceModel\Zone\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param TimezoneInterface $localeDate
     * @param Zone\Condition\CombineFactory $condCombineFactory
     * @param CollectionFactory $actionsCollectionFactory
     * @param ZoneCollectionFactory $collectionFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        CombineFactory $condCombineFactory,
        CollectionFactory $actionsCollectionFactory,
        ZoneCollectionFactory $collectionFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->condCombineFactory = $condCombineFactory;
        $this->actionsCollectionFactory = $actionsCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('MageWorx\ShippingRules\Model\ResourceModel\Zone');
        $this->setIdFieldName('entity_id');
    }

    /**
     * Validate model data
     *
     * @param DataObject $dataObject
     * @return bool|array
     */
    public function validateData(DataObject $dataObject)
    {
        $errors = parent::validateData($dataObject);

        if (!$dataObject->getName()) {
            $errors[] = __('Location Group name is required');
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }

    /**
     * Initialize zone model data from array.
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        return $this;
    }

    /**
     * Get zone condition combine model instance
     *
     * @return \MageWorx\ShippingRules\Model\Zone\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get zone actions instance
     *
     * @return \Magento\Rule\Model\Action\Collection
     */
    public function getActionsInstance()
    {
        $factory = $this->actionsCollectionFactory;
        $result = $factory->create();

        return $result;
    }

    /**
     * Detect valid zone for the address
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return $this|null
     */
    public function findZoneForAddress($address)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Zone\Collection $zoneCollection */
        $zoneCollection = $this->collectionFactory->create();
        $zoneCollection->addStoreFilter($address->getQuote()->getStore()->getId())
            ->addIsActiveFilter()
            ->setOrder('priority', AbstractDb::SORT_ORDER_ASC);

        /** @var \MageWorx\ShippingRules\Model\Zone $zone */
        foreach ($zoneCollection as $zone) {
            if ($zone->validate($address)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Retrieve zone description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData('description');
    }

    /**
     * Check is zone active
     *
     * @return int|bool
     */
    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    /**
     * Zones sort order (priority)
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->getData('priority');
    }

    /**
     * Get created at date
     *
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    /**
     * Get last updated date
     *
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    /**
     * Get serialized zones conditions
     *
     * @return string
     */
    public function getConditionsSerialized()
    {
        return $this->getData('conditions_serialized');
    }

    /**
     * Default shipping method code
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return $this->getData('default_shipping_method');
    }

    /**
     * Retrieve zone name
     *
     * If name is no declared, then default_name is used
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Alias for the getEntityId method
     *
     * @return int
     */
    public function getZoneId()
    {
        return $this->getEntityId();
    }
}
