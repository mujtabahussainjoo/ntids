<?php
namespace Ibnab\OwlSlider\Model\ResourceModel\SliderProduct;
use Ibnab\OwlSlider\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'product_id';
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\SliderProduct', 'Ibnab\OwlSlider\Model\ResourceModel\SliderProduct');
    }
    public function joinBanners()
    {
        $this->getSelect()
            ->joinLeft(array('products' => $this->getTable('catalog_product_entity')), 'banners.entity_id = main_table.banner_id', array('order' => 'order'));
         $this->getSelect()->group('main_table.slider_id');
         return $this;
    }
}
