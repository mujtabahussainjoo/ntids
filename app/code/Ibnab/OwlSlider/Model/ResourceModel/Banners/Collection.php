<?php
namespace Ibnab\OwlSlider\Model\ResourceModel\Banners;
use Ibnab\OwlSlider\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\Banners', 'Ibnab\OwlSlider\Model\ResourceModel\Banners');
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }
    protected function _afterLoad()
    {
        $this->performAfterLoad('ibnab_owlslider_banners_store', 'banner_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }
     /*
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('ibnab_owlslider_banners_store', 'banner_id');
    }

}
