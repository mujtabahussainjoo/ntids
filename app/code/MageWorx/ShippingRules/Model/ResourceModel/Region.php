<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ResourceModel;

use Magento\Directory\Model\ResourceModel\Region as OriginalRegion;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use MageWorx\ShippingRules\Model\Region as RegionModel;

class Region extends OriginalRegion
{
    protected $markNew;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        ResolverInterface $localeResolver,
        $connectionName = null
    ) {
        parent::__construct($context, $localeResolver, $connectionName);
    }

    /**
     * Define main and locale region name tables
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('directory_country_region', 'region_id');
        $this->_regionNameTable = $this->getTable('directory_country_region_name');
    }

    /**
     * Save New Object
     *
     * @param AbstractModel $object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function saveNewObject(AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $this->markNew = true;
        }

        parent::saveNewObject($object);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        /** @var \MageWorx\ShippingRules\Model\Region $object */
        if (!$object->getRegionId()) {
            return $this;
        }

        if ($this->markNew) {
            $isCustom =  true;
        } else {
            $isCustom =  $object->getIsCustom();
        }
        $this->markNew = false;
        $extendedRegionsData = [
            'region_id' => $object->getRegionId(),
            'is_active' => $object->getIsActive(),
            'is_custom' => $isCustom,
        ];
        $extendedRegionsTable = $this->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME);
        $this->getConnection()->insertOnDuplicate($extendedRegionsTable, $extendedRegionsData, ['is_active', 'is_custom']);

        $regionNameData = [
            'locale' => 'en_US',
            'region_id' => $object->getRegionId(),
            'name' => $object->getName()
        ];
        $originalRegionsNameTable = $this->getTable('directory_country_region_name');
        $this->getConnection()->insertOnDuplicate($originalRegionsNameTable, $regionNameData, ['locale', 'name']);

        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \MageWorx\ShippingRules\Model\Region|AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $alias = 'extended_regions_data';
        $cond = $this->getTable('directory_country_region').'.region_id =  extended_regions_data.region_id';
        $columns = [
            'is_active',
            'is_custom'
        ];
        $select->joinLeft([$alias => $this->getTable(RegionModel::EXTENDED_REGIONS_TABLE_NAME)], $cond, $columns);

        return $select;
    }

    /**
     * Safe delete, because we cant delete original regions
     *
     * @param RegionModel|AbstractModel $object
     * @return $this|void
     * @throws \Exception
     */
    public function delete(AbstractModel $object)
    {
        if (!$object->getIsCustom()) {
            $object->setIsActive(false);
            $object->setIsCustom(false);
            $this->save($object);

            return $this;
        }

        return parent::delete($object);
    }
}
