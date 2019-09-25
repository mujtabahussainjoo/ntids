<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel\Rate;

use MageWorx\ShippingRules\Model\Carrier as CarrierModel;

class Collection extends \MageWorx\ShippingRules\Model\ResourceModel\AbstractCollection
{
    /**
     * Store associated with rate entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::RATE_TABLE_NAME . '_store',
            'main_table_id_field' => 'rate_id',
            'linked_table_id_field' => 'entity_id',
            'entity_id_field' => 'store_id',
        ]
    ];

    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MageWorx\ShippingRules\Model\Carrier\Method\Rate',
            'MageWorx\ShippingRules\Model\ResourceModel\Rate'
        );
        $this->_map['fields']['rate_id'] = 'main_table.rate_id';
        $this->_setIdFieldName('rate_id');
    }

    /**
     * Redeclare before load method for adding sort order
     *
     * @return \MageWorx\ShippingRules\Model\ResourceModel\Rate\Collection
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->addOrder('priority', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Convert items array to array for select options
     *
     * return items array
     * array(
     *      $index => array(
     *          'value' => mixed
     *          'label' => mixed
     *      )
     * )
     *
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'rate_id', $labelField = 'title', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
