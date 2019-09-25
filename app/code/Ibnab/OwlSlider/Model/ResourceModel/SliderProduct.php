<?php
namespace Ibnab\OwlSlider\Model\ResourceModel;

class SliderProduct extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ibnab_owlslider_sliders_products', 'slider_id');
    }
}
