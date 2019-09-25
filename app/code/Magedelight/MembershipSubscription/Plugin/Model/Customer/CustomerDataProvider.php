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

namespace Magedelight\MembershipSubscription\Plugin\Model\Customer;

class CustomerDataProvider
{
    
     /**
      *
      * @var MembershipOrdersFactory
      */
    protected $_MembershipOrdersFactory;
    
    /**
     *
     * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
     * @param array $data
     */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        array $data = []
    ) {
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
    }
    
    
    /**
     *
     * @param \Magento\Customer\Model\Customer\DataProvider $provider
     * @param type $result
     * @return array
     */
    public function afterGetData(\Magento\Customer\Model\Customer\DataProvider $provider, $result)
    {
        if (isset($result)) {
            foreach ($result as $customer => $items) {
                $entity_id = $result[$customer]['customer']['entity_id'];
                $planExpiryDate = $this->getPlanExpiryDate($entity_id);
                
                if (!empty($planExpiryDate) && $planExpiryDate != "0000-00-00 00:00:00") {
                    $expiryDate = date('Y-m-d', strtotime($planExpiryDate));
                    $result[$customer]['customer']['membership_expiry_date'] = $expiryDate;
                }
            }
        }

        return $result;
    }
    
    
    /**
     *
     * @param type $customerId
     * @return boolean
     */
    public function getPlanExpiryDate($customerId)
    {
        $model = $this->_MembershipOrdersFactory->create()->getCollection();
        $model->addFieldToFilter('customer_id', $customerId);
        $model->addFieldToFilter('order_status', 'complete');
        $model->addFieldToFilter('plan_expiry_status', 0);
        $model->setOrder('membership_order_id', 'DESC');
        $data = $model->getData();
        if (count($data)>0) {
            return $data[0]['plan_expiry_date'];
        } else {
            return false;
        }
    }
}
