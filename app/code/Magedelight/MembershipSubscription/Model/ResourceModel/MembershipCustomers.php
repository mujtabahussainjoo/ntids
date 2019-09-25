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

namespace Magedelight\MembershipSubscription\Model\ResourceModel;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MembershipCustomers extends AbstractDb
{
 
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('magedelight_membership_orders', 'membership_order_id');
    }
}
