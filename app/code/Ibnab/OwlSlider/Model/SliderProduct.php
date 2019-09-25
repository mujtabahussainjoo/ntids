<?php
namespace Ibnab\OwlSlider\Model;

class SliderProduct extends \Magento\Framework\Model\AbstractModel
{
    

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\ResourceModel\SliderProduct');
    }
    public function getSelectedSliderProducts($slider_id)
    {
        return $this->getCollection()->addFieldToFilter('main_table.slider_id',['eq' => $slider_id]);
    }
}
