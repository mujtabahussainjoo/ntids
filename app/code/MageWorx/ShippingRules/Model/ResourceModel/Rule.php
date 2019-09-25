<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Sales Rule resource model
 */
class Rule extends \Magento\Rule\Model\ResourceModel\AbstractResource
{
    /**
     * Store associated with rule entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => 'mageworx_shippingrules_store',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'store_id',
        ],
        'customer_group' => [
            'associations_table' => 'mageworx_shippingrules_customer_group',
            'rule_id_field' => 'rule_id',
            'entity_id_field' => 'customer_group_id',
        ],
    ];

    /**
     * @var array
     */
    protected $customerGroupIds = [];

    /**
     * @var array
     */
    protected $storeIds = [];

    /**
     * Serializable field: amounts
     *
     * @var array
     */
    protected $_serializableFields = [
        'amount' => [null, []],
        'action_type' => [null, []],
        'shipping_methods' => [null, []],
        'disabled_shipping_methods' => [null, []],
        'enabled_shipping_methods' => [null, []],
        'store_errmsgs' => [null, []],
        'changed_titles' => [null, []]
    ];

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $storeModel;

    /**
     * Rule constructor.
     *
     * @param Context $context
     * @param \Magento\Store\Model\System\Store $storeModel
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\System\Store $storeModel,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->storeModel = $storeModel;
    }

    /**
     * Initialize main table and table id field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageworx_shippingrules', 'rule_id');
    }

    /**
     * Add customer group ids and store ids to rule data after load
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadCustomerGroupIds($object);
        $this->loadStoreIds($object);
        $this->prepareChangedTitles($object);
        parent::_afterLoad($object);

        return $this;
    }

    /**
     * Set changed titles including newly create store view ids
     *
     * @param $object
     * @return AbstractModel
     */
    protected function prepareChangedTitles(AbstractModel $object)
    {
        /** @var \MageWorx\ShippingRules\Api\Data\RuleInterface $object */
        $titles = $object->getChangedTitles();
        if (empty($titles)) {
            return $object;
        }

        $stores = $this->storeModel->getStoreCollection();
        foreach ($titles as $key => $data) {
            foreach ($stores as $storeId => $store) {
                if (empty($data['title_' . $storeId])) {
                    $titles[$key]['title_' . $storeId] = "";
                }
            }
        }

        $object->setChangedTitles($titles);

        return $object;
    }

    /**
     * @param AbstractModel $object
     * @return void
     */
    public function loadCustomerGroupIds(AbstractModel $object)
    {
        if (!$this->customerGroupIds) {
            $this->customerGroupIds = (array)$this->getCustomerGroupIds($object->getId());
        }
        $object->setData('customer_group_ids', $this->customerGroupIds);
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
     * Retrieve store ids of specified rule
     *
     * @param int $ruleId
     * @return array
     */
    public function getStoreIds($ruleId)
    {
        return $this->getAssociatedEntityIds($ruleId, 'store');
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    public function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);
        if ($object->getChangedTitles()) {
            $changedTitles = $object->getChangedTitles();
            if (is_array($changedTitles) && !empty($changedTitles['__empty'])) {
                unset($changedTitles['__empty']);
                $object->setChangedTitles($changedTitles);
            }
        }

        return $this;
    }

    /**
     * Bind shipping rule to customer group(s) and store(s).
     * Save rule's associated store labels.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
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

        if ($object->hasCustomerGroupIds()) {
            $customerGroupIds = $object->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', (string)$customerGroupIds);
            }
            $this->bindRuleToEntity($object->getId(), $customerGroupIds, 'customer_group');
        }

        return parent::_afterSave($object);
    }
}
