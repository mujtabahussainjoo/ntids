<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel\Slide;

use Aheadworks\Rbslider\Model\ResourceModel\AbstractCollection;
use Aheadworks\Rbslider\Model\Slide;
use Aheadworks\Rbslider\Model\ResourceModel\Slide as ResourceSlide;
use Magento\Store\Model\Store;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Aheadworks\Rbslider\Model\ResourceModel\Slide
 */
class Collection extends AbstractCollection
{
    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Slide::class, ResourceSlide::class);
    }

    /**
     * Join slide position in banner
     *
     * @param int $bannerId
     * @return $this
     */
    public function joinPositions($bannerId)
    {
        if (!$this->getFlag('slide_positions_joined')) {
            $this->getSelect()->joinLeft(
                ['pos' => $this->getTable('aw_rbslider_slide_banner')],
                'main_table.id = pos.slide_id AND pos.banner_id = ' . $bannerId,
                ['position' => 'IFNULL(pos.position, 0)']
            );
            $this->addFilterToMap('position', 'pos.position');
            $this->setFlag('slide_positions_joined', true);
        }

        return $this;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $slides = parent::_toOptionArray('id', 'name');
        if (!count($slides)) {
            array_unshift(
                $slides,
                ['value' => 0, 'label' => __('No slides found')]
            );
        }
        return $slides;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'display_from' || $field == 'display_to') {
            // Fix if apply filter on display_from or display_to in grid
            $resultCondition = $this->_translateCondition($field, ['null' => ''])
                . ' OR ' . $this->_translateCondition($field, $condition);
            return $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        }
        if ($field == 'store_id') {
            return $this->addStoreFilter($condition);
        }
        if ($field == 'customer_group_id') {
            return $this->addCustomerGroupFilter($condition);
        }
        if ($field == 'id') {
            return parent::addFieldToFilter('main_table.' . $field, $condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param int|array $store
     * @return $this
     */
    public function addStoreFilter($store)
    {
        if (!is_array($store)) {
            $store = [$store];
        }
        $store[] = Store::DEFAULT_STORE_ID;
        $this->addFilter('store_id', ['in' => $store], 'public');

        return $this;
    }

    /**
     * Add customer group filter
     *
     * @param int|array $customerGroup
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroup)
    {
        if (!is_array($customerGroup)) {
            $customerGroup = [$customerGroup];
        }
        $this->addFilter('customer_group_id', ['in' => $customerGroup], 'public');

        return $this;
    }

    /**
     * Add date filter
     *
     * @param string $currentDate
     * @return $this
     */
    public function addDateFilter($currentDate)
    {
        $this
            ->getSelect()
            ->where(
                '(main_table.display_from IS NULL OR main_table.display_from <= "' . $currentDate . '")
                AND (main_table.display_to IS NULL OR main_table.display_to >= "' . $currentDate . '")'
            );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'position' && $direction == self::SORT_ORDER_DESC) {
            $this->getSelect()->order(new \Zend_Db_Expr('RAND()'));
            return $this;
        } else {
            return parent::addOrder($field, $direction);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable('aw_rbslider_slide_banner', 'id', 'slide_id', 'banner_id', 'banner_ids');
        $this->attachRelationTable('aw_rbslider_slide_store', 'id', 'slide_id', 'store_id', 'store_ids');
        $this->attachRelationTable(
            'aw_rbslider_slide_customer_group',
            'id',
            'slide_id',
            'customer_group_id',
            'customer_group_ids'
        );
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable('aw_rbslider_slide_banner', 'id', 'slide_id', 'banner_id');
        $this->joinLinkageTable('aw_rbslider_slide_store', 'id', 'slide_id', 'store_id');
        $this->joinLinkageTable('aw_rbslider_slide_customer_group', 'id', 'slide_id', 'customer_group_id');
        parent::_renderFiltersBefore();
    }
}
