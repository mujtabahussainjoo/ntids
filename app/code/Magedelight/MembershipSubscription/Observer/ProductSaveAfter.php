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

class ProductSaveAfter implements ObserverInterface
{

    
    /**
     * Group repository
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    
    /**
     * Group data factory
     *
     * @var \Magento\Customer\Api\Data\GroupInterfaceFactory
     */
    protected $groupDataFactory;
    
    /**
     * Customer group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;

    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;

    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    
    /**
     *
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\Data\GroupInterfaceFactory $groupDataFactory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->groupDataFactory = $groupDataFactory;
        $this->_customerGroup = $customerGroup;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->request = $request;
    }
    
    
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        
        if ((!empty($product)) && ($product->getTypeId() == "Membership")) {
            $productId = $product->getId();
            $requestId = $this->request->getParam('id');
            $productName =  $product->getName();
            $relatedCustomerGroupId =  $product->getRelatedCustomerGroupId();
            $membershipDuration = serialize($product->getMembershipDuration());
            $featured = $product->getFeatured();
            
            if (empty($requestId)) {
                $customerGroup = $this->groupDataFactory->create();
                $customerGroup->setCode($productName);
                $this->groupRepository->save($customerGroup);
                $customerGroupId = $this->getCustomerGroupId($requestId);
            } else {
                $customerGroupId = $this->getCustomerGroupId($requestId);
                $customerGroup = $this->groupRepository->getById($customerGroupId);
                $customerGroup->setCode($productName);
                $this->groupRepository->save($customerGroup);
            }
            
            if ($customerGroupId) {
                $this->saveMembershipProduct($requestId, $productId, $productName, $membershipDuration, $featured, $customerGroupId, $relatedCustomerGroupId);
            }
        }
    }
    
    
    /**
     * Get customer group id
     *
     * @param type $productId
     * @return int
     */
    public function getCustomerGroupId($productId)
    {
        if (empty($productId)) {
            $customerGroups = $this->_customerGroup->toOptionArray();
            $lastGroup = end($customerGroups);
            $customerGroupsId = $lastGroup['value'];
            return $customerGroupsId;
        } else {
            $model = $this->_MembershipProductsFactory->create();
            $model->load($productId, 'product_id');
            return $model->getCustomerGroupId();
        }
    }
    
    
    /**
     * Save Membership Product
     *
     * @param type $requestId
     * @param type $productId
     * @param type $productName
     * @param type $customerGroupId
     */
    public function saveMembershipProduct($requestId, $productId, $productName, $membershipDuration, $featured, $customerGroupId, $relatedCustomerGroupId)
    {
        
        $model = $this->_MembershipProductsFactory->create();
        
        if (empty($requestId)) {
            $data = ['product_name'=>$productName,'membership_duration'=>$membershipDuration,'product_id'=>$productId,'featured'=>$featured,'customer_group_id'=>$customerGroupId,'related_customer_group_id'=>$customerGroupId];
        } else {
            //load model
            $model->load($productId, 'product_id');
            $data = ['product_name'=>$productName,'membership_duration'=>$membershipDuration,'featured'=>$featured,'related_customer_group_id'=>$relatedCustomerGroupId];
        }
        
        $model->addData($data);
        
        $model->save();
    }
}
