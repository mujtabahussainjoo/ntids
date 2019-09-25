<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Model\ResourceModel\MembershipOrders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{

    /**
     * Event object
     *
     * @var string
     */
    protected $_idFieldName = 'membership_order_id';

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        
        $this->_init('Magedelight\MembershipSubscription\Model\MembershipOrders', 'Magedelight\MembershipSubscription\Model\ResourceModel\MembershipOrders');
    }
}
