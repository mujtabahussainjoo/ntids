<?php
namespace Ibnab\OwlSlider\Model\ResourceModel;

class BannerSlider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ibnab_owlslider_sliders_banners', 'slider_id');
    }
}
