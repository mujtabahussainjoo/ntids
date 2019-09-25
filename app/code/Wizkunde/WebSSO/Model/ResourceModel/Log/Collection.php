<?php

namespace Wizkunde\WebSSO\Model\ResourceModel\Log;

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
            'Wizkunde\WebSSO\Model\Log',
            'Wizkunde\WebSSO\Model\ResourceModel\Log'
        );
    }
}
