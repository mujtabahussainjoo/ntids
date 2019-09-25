<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel\Banner;

use Aheadworks\Rbslider\Model\Banner;
use Aheadworks\Rbslider\Model\ResourceModel\Banner as ResourceBanner;
use Aheadworks\Rbslider\Model\ResourceModel\AbstractCollection;
use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Model\Source\Position;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Aheadworks\Rbslider\Model\ResourceModel\Banner
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
        $this->_init(Banner::class, ResourceBanner::class);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $optionArray = ['' => ' '];
        foreach ($this->toOptionArray() as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $banners = parent::_toOptionArray('id', 'name');
        if (!count($banners)) {
            array_unshift(
                $banners,
                ['value' => 0, 'label' => __('No banners found')]
            );
        }
        return $banners;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'position') {
            // Fix if apply filter on position and Fix if apply filter on position and slide id
            $resultCondition = $this->_translateCondition('page_type', ['neq' => PageType::CUSTOM_WIDGET])
                . ' AND ' . $this->_translateCondition('main_table.' . $field, $condition);
            return $this->getSelect()->where($resultCondition, null, Select::TYPE_CONDITION);
        }
        if ($field == 'id') {
            return parent::addFieldToFilter('main_table.' . $field, $condition);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'position') {
            // Fix if apply sorting on position
            $field = 'sort_position';
            $this->getSelect()->columns(
                new \Zend_Db_Expr(
                    'IF(main_table.position = ' . Position::CONTENT_TOP
                    . ' AND main_table.page_type = ' . PageType::CUSTOM_WIDGET
                    . ', "", main_table.position) as ' . $field
                )
            );
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable('aw_rbslider_slide_banner', 'id', 'banner_id', 'slide_id', 'slide_ids');
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable('aw_rbslider_slide_banner', 'id', 'banner_id', 'slide_id');
        parent::_renderFiltersBefore();
    }
}
