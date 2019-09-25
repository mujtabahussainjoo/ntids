<?php
namespace Ibnab\OwlSlider\Model\ResourceModel\BannerStore;
use Ibnab\OwlSlider\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\BannerStore', 'Ibnab\OwlSlider\Model\ResourceModel\BannerStore');
    }
}
