<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel\Method;

use MageWorx\ShippingRules\Model\Carrier as CarrierModel;

class Collection extends \MageWorx\ShippingRules\Model\ResourceModel\AbstractCollection
{
    /**
     * @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory
     */
    protected $carrierCollectionFactory;

    /**
     * Store associated with method entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap = [
        'store' => [
            'associations_table' => CarrierModel::METHOD_TABLE_NAME . '_store',
            'main_table_id_field' => 'entity_id',
            'linked_table_id_field' => 'entity_id',
            'entity_id_field' => 'store_id',
        ]
    ];

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $carrierCollectionFactory
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory $carrierCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $date,
            $storeManager,
            $connection,
            $resource
        );
        $this->carrierCollectionFactory = $carrierCollectionFactory;
    }

    /**
     * Set resource model and determine field mapping
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MageWorx\ShippingRules\Model\Carrier\Method', 'MageWorx\ShippingRules\Model\ResourceModel\Method');
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
        $this->_setIdFieldName('entity_id');
    }

    /**
     * @param $carrierId
     * @return $this
     */
    public function addCarrierFilter($carrierId)
    {
        $this->addFieldToFilter('carrier_id', $carrierId);

        return $this;
    }

    /**
     * Convert collection to array of allowed methods
     * @see \MageWorx\ShippingRules\Model\Carrier\Artificial::getAllowedMethods
     *
     * @return array
     */
    public function toAllowedMethodsArray()
    {
        $arrItems = [];
        foreach ($this as $item) {
            $arrItems[$item->getData('code')] = $item->getData('title');
        }
        return $arrItems;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @param bool|false $withCarrier
     *
     * @return array
     */
    public function toOptionArray(
        $valueField = 'entity_id',
        $labelField = 'title',
        $additional = [],
        $withCarrier = false
    ) {
        return $this->_toOptionArray($valueField, $labelField, $additional, $withCarrier);
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
     * @param bool $withCarrier
     *
     * @return array
     */
    protected function _toOptionArray(
        $valueField = 'entity_id',
        $labelField = 'title',
        $additional = [],
        $withCarrier = false
    ) {
        $res = [];
        $additional['value'] = $valueField;
        $additional['label'] = $labelField;

        if ($withCarrier) {
            /** @var \MageWorx\ShippingRules\Model\ResourceModel\Carrier\Collection $carrierCollection */
            $carrierCollection = $this->carrierCollectionFactory->create();
            $carrierCollection->load();
            $i = 0;
            /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
            foreach ($carrierCollection as $carrier) {
                $items = $this->getItemsByColumnValue('carrier_id', $carrier->getId());
                $dataByCarrier[$i] = [
                    'label' => $carrier->getTitle(),
                    'value' => []
                ];
                $data = [];
                foreach ($items as $item) {
                    $data[] = [
                            'label' => $item->getData($labelField),
                            'value' => $item->getData($valueField)
                    ];
                }
                if (!empty($data)) {
                    $dataByCarrier[$i]['value'] = $data;
                    $i++;
                }
            }
            if (!empty($dataByCarrier)) {
                $res = $dataByCarrier;
            }
        } else {
            /** @var \MageWorx\ShippingRules\Model\Carrier\Method $item */
            foreach ($this as $item) {
                foreach ($additional as $code => $field) {
                    $label = $item->getData($field);
                    $data[$code] = $label;
                }
                if (!empty($data)) {
                    $res[] = $data;
                }
            }
        }

        return $res;
    }
}
