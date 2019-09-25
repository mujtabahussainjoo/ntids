<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model\ResourceModel\LoginAttempts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
            $this->_init(
                        'Amasty\AdminActionsLog\Model\LoginAttempts',
            'Amasty\AdminActionsLog\Model\ResourceModel\LoginAttempts'
        );
    }
}
