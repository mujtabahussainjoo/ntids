<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel\Statistic;

use Aheadworks\Rbslider\Model\Statistic;
use Aheadworks\Rbslider\Model\ResourceModel\Statistic as ResourceStatistic;
use Aheadworks\Rbslider\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Aheadworks\Rbslider\Model\ResourceModel\Statistic
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
        $this->_init(Statistic::class, ResourceStatistic::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $ctrQuery = $this->getConnection()->select()
            ->from(
                [$this->getTable('aw_rbslider_statistic')],
                ['ctr_id' => 'id', 'ctr' => 'ROUND(click_count/IF(view_count > 0, view_count, 1) * 100, 0)']
            );
        $this->getSelect()
            ->joinLeft(
                ['stat' => $ctrQuery],
                'main_table.id = stat.ctr_id',
                ['ctr']
            );
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachSlideBanner();
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable('aw_rbslider_slide_banner', 'slide_banner_id', 'id', 'banner_id');
        $this->joinLinkageTable('aw_rbslider_slide_banner', 'slide_banner_id', 'id', 'slide_id');
        $this->joinNameField('slide_name', 'aw_rbslider_slide', 'slide_id');
        $this->joinNameField('banner_name', 'aw_rbslider_banner', 'banner_id');
        parent::_renderFiltersBefore();
    }

    /**
     * Attach slide and banner data to collection items
     *
     * @return void
     */
    private function attachSlideBanner()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                ['sb' => $this->getTable('aw_rbslider_slide_banner')],
                []
            )->joinLeft(
                ['s' => $this->getTable('aw_rbslider_slide')],
                'sb.slide_id = s.id',
                [
                    'slide_id' => 'id',
                    'slide_name' => 'name',
                    'img_type',
                    'img_file',
                    'img_url'
                ]
            )->joinLeft(
                ['b' => $this->getTable('aw_rbslider_banner')],
                'sb.banner_id = b.id',
                [
                    'banner_id' => 'id',
                    'banner_name' => 'name'
                ]
            )->where('sb.id = :id');
        /** @var \Magento\Framework\DataObject $item */
        foreach ($this as $item) {
            $data = $connection->fetchRow($select, ['id' => $item->getData('slide_banner_id')]);
            $item->setData('ctr', $item->getData('ctr') . '%');
            $item->addData($data);
        }
    }

    /**
     * Join field if sorting applied
     *
     * @param string $fieldName
     * @param string $tableName
     * @param string $linkageColumnName
     * @return void
     */
    private function joinNameField($fieldName, $tableName, $linkageColumnName)
    {
        if (array_key_exists($fieldName, $this->_orders)) {
            $this->getSelect()
                ->joinLeft(
                    [$fieldName . '_sb' => $this->getTable('aw_rbslider_slide_banner')],
                    'main_table.slide_banner_id = ' . $fieldName . '_sb.id',
                    []
                )->joinLeft(
                    [$fieldName . '_s' => $this->getTable($tableName)],
                    $fieldName . '_s.id = ' . $fieldName . '_sb.' . $linkageColumnName,
                    [$fieldName => 'name']
                );
        }
    }
}
