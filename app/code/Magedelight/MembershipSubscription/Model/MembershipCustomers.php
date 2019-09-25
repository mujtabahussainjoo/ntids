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

namespace Magedelight\MembershipSubscription\Model;

use Magento\Framework\Model\AbstractModel;

class MembershipCustomers extends AbstractModel
{

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Magedelight\MembershipSubscription\Model\ResourceModel\MembershipCustomers');
    }
    
    /**
     * Get Id.
     *
     * @return int
     */
    public function getMembershipOrderId()
    {
        return $this->getData(self::MembershipOrderId);
    }
    
    /**
     * Set Id.
     */
    public function setMembershipOrderId($membership_order_id)
    {
        return $this->setData(self::MembershipOrderId, $membership_order_id);
    }
}