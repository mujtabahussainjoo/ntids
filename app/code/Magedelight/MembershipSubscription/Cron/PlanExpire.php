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

namespace Magedelight\MembershipSubscription\Cron;

class PlanExpire
{
    /**
     * Membership factory
     *
     * @var MembershipOrdersFactory
     */
    protected $_MembershipOrdersFactory;
    
    /**
     * Membership factory
     *
     * @var MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;
    
    /**
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    
    /**
     *
     * @var DateTimeFactory
     */
    protected $dateFactory;
    
    
    /**
     *
     * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    ) {
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->customerRepository = $customerRepository;
        $this->_dateFactory = $dateFactory;
    }
    
   
    public function execute()
    {
//        echo "<pre>";
        $current_date = $this->_dateFactory->create()->gmtDate();
        $planExpireDate = date("Y-m-d", strtotime($current_date));
//        $planExpireDate = '2018-05-30 11:40:39';
        
        $orderCollection = $this->_MembershipOrdersFactory->create()->getCollection();
        $orderCollection->addFieldToFilter('order_status', 'complete');
        $orderCollection->addFieldToFilter('plan_expiry_date', ['like' => '%'.$planExpireDate.'%']);
        $orderCollection->addFieldToFilter('plan_expiry_status', 0);
//        echo $orderCollection->getSelect()->__toString();
        $orders = $orderCollection->getData();
        
        
//        print_r($orders);
//        exit;
        if (count($orders)>0) {
            foreach ($orders as $key => $value) {
                $membershipOrderID = $value['membership_order_id'];
                $customerId = $value['customer_id'];
                $data = ['plan_expiry_status'=>1];
                $Ordersmodel = $this->_MembershipOrdersFactory->create();
                $Ordersmodel->load($membershipOrderID, 'membership_order_id');
                $Ordersmodel->addData($data);
                $saveData = $Ordersmodel->save();
                if ($saveData) {
                    $customerGroup = $this->checkCustomerOtherPlans($customerId, $membershipOrderID);
                    if ($customerGroup) {
                        $groupId = $customerGroup;
                    } else {
                        $groupId = $this->getMembershipGroups($value['customer_past_group_id']);
                    }
//                    echo $groupId;
                    $customer = $this->customerRepository->getById($customerId);
                    $customer->setGroupId($groupId);
                    $this->customerRepository->save($customer);
                }
            }
        }
    }
    
    /**
     *
     * @param type $customerId
     * @param type $membershipOrderId
     * @return boolean
     */
    public function checkCustomerOtherPlans($customerId, $membershipOrderId)
    {
        $customerOrders = $this->_MembershipOrdersFactory->create()->getCollection();
        $customerOrders->addFieldToFilter('customer_id', $customerId);
        $customerOrders->addFieldToFilter('order_status', 'complete');
        $customerOrders->addFieldToFilter('plan_expiry_status', 0);
//        $customerOrders->addFieldToFilter('membership_order_id', array('lteq' => $membershipOrderId));
        $customerOrders->setOrder('membership_order_id', 'DESC');
//        echo $customerOrders->getSelect()->__toString();
        $customerData = $customerOrders->getData();
//        print_r($customerData);
//        exit;
        if (count($customerData)>0) {
            return $customerData[0]['related_customer_group_id'];
        } else {
            return false;
        }
    }
    
    /**
     *
     * @param type $customerGroupId
     * @return int
     */
    public function getMembershipGroups($customerGroupId)
    {
        $productModel = $this->_MembershipProductsFactory->create()->getCollection();
        $data = $productModel->getData();
        
        $membershipGroups = [];
        if (count($data)>0) {
            foreach ($data as $key => $value) {
                $membershipGroups[]  = $value['customer_group_id'];
            }
            
            if (in_array($customerGroupId, $membershipGroups)) {
                return 1;
            } else {
                return $customerGroupId;
            }
        }
    }
}
