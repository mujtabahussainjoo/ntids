<?php 

namespace Serole\GiftMessage\Model\ResourceModel\Image;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Serole\GiftMessage\Model\Image', 'Serole\GiftMessage\Model\ResourceModel\Image');
    }

}