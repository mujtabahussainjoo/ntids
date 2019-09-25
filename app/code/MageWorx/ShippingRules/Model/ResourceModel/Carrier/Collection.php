<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel\Carrier;

use MageWorx\ShippingRules\Model\Carrier as CarrierModel;

class Collection extends \MageWorx\ShippingRules\Model\ResourceModel\AbstractCollection
{
    /**
     * Store associated with carrier entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::CARRIER_TABLE_NAME . '_store',
            'main_table_id_field' => 'carrier_id',
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
            'MageWorx\ShippingRules\Model\Carrier',
            'MageWorx\ShippingRules\Model\ResourceModel\Carrier'
        );
        $this->_map['fields']['carrier_id'] = 'main_table.carrier_id';
        $this->_setIdFieldName('carrier_id');
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
    protected function _toOptionArray($valueField = 'carrier_id', $labelField = 'name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
