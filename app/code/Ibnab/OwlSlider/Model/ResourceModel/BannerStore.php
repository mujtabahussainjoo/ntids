<?php
namespace Ibnab\OwlSlider\Model\ResourceModel;

class BannerStore extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ibnab_owlslider_banners_store', 'banner_id');
    }
}
