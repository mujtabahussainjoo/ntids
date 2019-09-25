<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageWorx\ShippingRules\Model\Carrier as CarrierModel;
use Magento\Framework\Stdlib\StringUtils;
use MageWorx\ShippingRules\Helper\Data as Helper;
use Magento\Store\Model\StoreManagerInterface;

class Carrier extends AbstractResourceModel
{
    /**
     * Store associated with carrier entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::CARRIER_TABLE_NAME . '_store',
            'ref_id_field' => 'entity_id',
            'entity_id_field' => 'store_id',
        ]
    ];

    /**
     * @var Method\CollectionFactory
     */
    protected $methodsCollectionFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \MageWorx\ShippingRules\Helper\Data $helper
     * @param StoreManagerInterface $storeManager
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Method\CollectionFactory $methodsCollectionFactory
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        StringUtils $string,
        Helper $helper,
        StoreManagerInterface $storeManager,
        \MageWorx\ShippingRules\Model\ResourceModel\Method\CollectionFactory $methodsCollectionFactory,
        $connectionName = null
    ) {
        $this->methodsCollectionFactory = $methodsCollectionFactory;
        parent::__construct($context, $string, $helper, $storeManager, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CarrierModel::CARRIER_TABLE_NAME, 'carrier_id');
    }

    /**
     * Add customer group ids and store ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier $object */
        parent::_afterLoad($object);
        $this->addMethods($object);
        $storeId = $this->storeManager->getStore()->getId();
        $label = $object->getStoreLabel($storeId);
        if ($label) {
            $object->setTitle($label);
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);
        $this->validateModel($object);
        return $this;
    }

    /**
     * Validate model required fields
     *
     * @param AbstractModel $object
     * @throws LocalizedException
     */
    public function validateModel(AbstractModel $object)
    {
        /** @var Carrier $object */
        if (!$object->getCarrierCode()) {
            throw new LocalizedException(__('Carrier Code is required'));
        }
    }

    /**
     * Get store labels table
     *
     * @return string
     */
    protected function getStoreLabelsTable()
    {
        return $this->getTable(CarrierModel::CARRIER_LABELS_TABLE_NAME);
    }

    /**
     * Get reference id column name from the labels table
     *
     * @return string
     */
    protected function getStoreLabelsTableRefId()
    {
        return 'carrier_id';
    }

    /**
     * Adds corresponding shipping methods to the carrier
     * @param AbstractModel $object
     */
    public function addMethods(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Method\Collection $methods */
        $methods = $this->getMethodsCollection($object);
        $object->setMethods($methods->getItems());
    }

    /**
     * @param AbstractModel $object
     * @return Method\Collection
     */
    public function getMethodsCollection(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Method\Collection $methods */
        $methods = $this->methodsCollectionFactory->create();
        $methods->addFieldToFilter('carrier_id', $object->getId());
        $object->setMethodsCollection($methods);

        return $methods;
    }
}
