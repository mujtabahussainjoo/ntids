<?php
namespace Ibnab\OwlSlider\Model\ResourceModel\Sliders;
use Ibnab\OwlSlider\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'slider_id';
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\Sliders', 'Ibnab\OwlSlider\Model\ResourceModel\Sliders');
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
}
