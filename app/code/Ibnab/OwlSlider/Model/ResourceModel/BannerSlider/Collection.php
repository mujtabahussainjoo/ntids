<?php
namespace Ibnab\OwlSlider\Model\ResourceModel\BannerSlider;
use Ibnab\OwlSlider\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\BannerSlider', 'Ibnab\OwlSlider\Model\ResourceModel\BannerSlider');
    }
    public function joinBanners()
    {
        $this->getSelect()
            ->joinLeft(array('banners' => $this->getTable('ibnab_owlslider_banners')), 'banners.banner_id = main_table.banner_id', array('order' => 'order'));
         $this->getSelect()->group('main_table.slider_id');
         return $this;
    }
}
