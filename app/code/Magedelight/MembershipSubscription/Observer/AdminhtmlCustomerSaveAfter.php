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

class AdminhtmlCustomerSaveAfter implements ObserverInterface
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
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;

    /**
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     *
     * @param \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipOrdersFactory $MembershipOrdersFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->_MembershipOrdersFactory = $MembershipOrdersFactory;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
    }
    
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();
        $customerGroup = $customer->getGroupId();
        
        $requestCustomer = $this->request->getParam('customer');
        
        if (isset($requestCustomer['membership_expiry_date']) && !empty($customerId)) {
            $membershipData = $this->getMembershipOrderID($customerId);
            
            $membershipOrderID = $membershipData['membership_order_id'];
            
            $membershipGroups = $this->getMembershipGroups($membershipData['related_customer_group_id']);
            
            if ($membershipOrderID) {
                if (!in_array($customerGroup, $membershipGroups)) {
                    $membershipExpiryDate = "";
                } elseif (empty($requestCustomer['membership_expiry_date'])) {
                    $this->messageManager->addError(__(' Membership expiry date cannot be empty for this group.'));
                    return $exception;
                } else {
                    $membershipExpiryDate = date("Y-m-d H:i:s", strtotime($requestCustomer['membership_expiry_date']));
                }
                
                $CurrentCustomergroupId = $requestCustomer['group_id'];
                
                $data = ['current_customer_group_id'=>$CurrentCustomergroupId,'plan_expiry_status'=>0,'plan_expiry_date'=>$membershipExpiryDate];
                
                $Ordersmodel = $this->_MembershipOrdersFactory->create();
                $Ordersmodel->load($membershipOrderID, 'membership_order_id');
                
                $Ordersmodel->addData($data);
                $Ordersmodel->save();
            } elseif (in_array($customerGroup, $membershipGroups)) {
                $this->messageManager->addError(__('Sorry this customer not purchased membership for this group.'));
                return $exception;
            }
        }
    }
    
    /**
     *
     * @param type $customerId
     * @return boolean
     */
    public function getMembershipOrderID($customerId)
    {
        $model = $this->_MembershipOrdersFactory->create()->getCollection();
        $model->addFieldToFilter('customer_id', $customerId);
        $model->addFieldToFilter('order_status', 'complete');
        $model->addFieldToFilter('plan_expiry_status', 0);
        $model->setOrder('membership_order_id', 'DESC');
        $data = $model->getData();
        if (count($data)>0) {
            return $data[0];
        } else {
            return false;
        }
    }
    
    /**
     * Get all membership products and current customer's related group as a membership group
     * @return array
     */
    public function getMembershipGroups($RelatedCustomerGroupId)
    {
        $productModel = $this->_MembershipProductsFactory->create()->getCollection();
        $data = $productModel->getData();
        
        $relatedCustomerGroups = [];
        if (count($data)>0) {
            foreach ($data as $key => $value) {
                $relatedCustomerGroups[]  = $value['customer_group_id'];
            }
            
            if ($RelatedCustomerGroupId) {
                $relatedCustomerGroups[] = $RelatedCustomerGroupId;
            }
        }
        
        return $relatedCustomerGroups;
    }
}
