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

namespace Magedelight\MembershipSubscription\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveBefore implements ObserverInterface
{

    
    /**
     * Membership factory
     *
     * @var MembershipOrdersFactory
     */
    protected $_MembershipOrdersFactory;

    /**
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    
   /**
    *
    * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
    * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    * @param array $data
    */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->customerRepository = $customerRepository;
    }
    
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        if ($order instanceof \Magento\Framework\Model\AbstractModel) {
            if ($order->getState() == 'complete') {
                foreach ($order->getAllItems() as $item) {
                    if ((count($item)>0) && ($item->getProductType() == "Membership")) {
                        $orderId = $item->getOrderId();
                        $Ordersmodel = $this->_MembershipOrdersFactory->create();
                        $Ordersmodel->load($orderId, 'order_id');
                        $membershipOrder = $Ordersmodel->getData();
                        
                        if (count($membershipOrder)>0) {
                            $customerId = $order->getCustomerId();
                            $orderStatus = $order->getState();
                            $customerPastGroup = $order->getCustomerGroupId();
                            $planExpiryDate = $this->getCustomerPlanExpiryDate($membershipOrder['customer_plan']);
                            
                            $groupId = $membershipOrder['related_customer_group_id'];
                            
                            if ($planExpiryDate) {
                                $data = ['order_status'=>$orderStatus,'customer_past_group_id'=>$customerPastGroup,'current_customer_group_id'=>$groupId,'plan_expiry_date'=>$planExpiryDate];
                                $Ordersmodel->addData($data);
                                $Ordersmodel->save();
                            }
                            
                            $this->assignCustomerGroup($customerId, $groupId);
                            $order->setCustomerGroupId($groupId);
                        }
                    }
                }
            }
        }
    }
    
    
    /**
     *
     * @param type $orderId
     * @return array
     */
    public function getCustomerPlanExpiryDate($customerPlan)
    {
        if (!empty($customerPlan)) {
            $customerPlanArray = unserialize($customerPlan);
            $duration = $customerPlanArray['duration'];
            $durationUnit = $customerPlanArray['duration_unit'];

            $shortDate = "+".$duration." ".$durationUnit;
            $increaseDate = strtotime($shortDate);
            $planExpiryDate = date("Y-m-d h:i:s", $increaseDate);
            return $planExpiryDate;
        }
    }
    
    /**
     *
     * @param type $customerId
     * @param type $groupId
     */
    public function assignCustomerGroup($customerId, $groupId)
    {
        if (!empty($customerId)) {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setGroupId($groupId);
            $this->customerRepository->save($customer);
        }
    }
}
