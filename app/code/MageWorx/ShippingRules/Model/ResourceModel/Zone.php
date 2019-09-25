<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use MageWorx\ShippingRules\Model\Zone as ZoneModel;
use Magento\Framework\App\ObjectManager;

class Zone extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Store associated with zone entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => ZoneModel::ZONE_STORE_TABLE_NAME,
            'rule_id_field' => 'zone_id',
            'entity_id_field' => 'store_id',
        ],
    ];

    /**
     * @var array
     */
    protected $storeIds = [];

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        $connectionName = null
    ) {
        $this->string = $string;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\MageWorx\ShippingRules\Model\Zone::ZONE_TABLE_NAME, 'entity_id');
    }

    /**
     * Add store ids to zone data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadStoreIds($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadStoreIds(AbstractModel $object)
    {
        $this->storeIds = (array)$this->getStoreIds($object->getId());
        $object->setData('store_ids', $this->storeIds);
    }

    /**
     * Retrieve store ids of specified zone
     *
     * @param int $zoneId
     * @return array
     */
    public function getStoreIds($zoneId)
    {
        return $this->getAssociatedEntityIds($zoneId, 'store');
    }

    /**
     * Bind shipping zone to the store(s).
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->hasStoreIds()) {
            $storeIds = $object->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', (string)$storeIds);
            }
            $this->bindRuleToEntity($object->getId(), $storeIds, 'store');
        }

        return parent::_afterSave($object);
    }
}
