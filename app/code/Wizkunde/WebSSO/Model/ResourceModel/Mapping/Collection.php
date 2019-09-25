<?php

namespace Wizkunde\WebSSO\Model\ResourceModel\Mapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Wizkunde\WebSSO\Model\Mapping',
            'Wizkunde\WebSSO\Model\ResourceModel\Mapping'
        );
    }
}
