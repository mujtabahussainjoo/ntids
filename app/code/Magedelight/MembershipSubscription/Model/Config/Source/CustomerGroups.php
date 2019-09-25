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

namespace Magedelight\MembershipSubscription\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CustomerGroups implements ArrayInterface
{
    /**
     * Customer Group
     *
     * @var \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected $_customerGroup;
    
    /**
     * Http Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;
    
    
    /**
     *
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Magento\Framework\App\Request\Http $request,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        array $data = []
    ) {
        $this->_customerGroup = $customerGroup;
        $this->request = $request;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
    }
    
    
    /**
     * @return array
     */
    public function toOptionArray()
    {

        $model = $this->getCurrentCustomerGroup();
        
        
       
        
        
        if ($model) {
            $relatedCustomerGroupId = $model->getRelatedCustomerGroupId();
            $customerGroups = $this->getCustomerGroups();
            
            if (!empty($customerGroups)) {
                foreach ($customerGroups as $key => $value) {
                    if ($value['value']==$relatedCustomerGroupId) {
                        unset($customerGroups[$key]);

                        $newOpuion['value'] = $value['value'];
                        $newOpuion['label'] = $value['label'];

                        array_unshift($customerGroups, $newOpuion);
                    }
                }

                return $customerGroups;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
    
    /**
     * get customer group option
     *
     * @return boolean
     */
    public function getCurrentCustomerGroup()
    {
        
        $productId = $this->request->getParam('id');
        if ($productId) {
            $model = $this->_MembershipProductsFactory->create();
            return $model->load($productId, 'product_id');
        } else {
            return false;
        }
    }
    
    /**
     * get customer group
     *
     * @return type
     */
    public function getCustomerGroups()
    {
        
        $model = $this->_MembershipProductsFactory->create()->getCollection();
        $modelData = $model->getData();

        $currentModel =  $this->getCurrentCustomerGroup();
        $currentCustomerGroup =  $currentModel->getCustomerGroupId();
        
        $membershipCustomerGroups = [];
        
        foreach ($modelData as $key => $field) {
            if ($currentCustomerGroup != $field['customer_group_id']) {
                $membershipCustomerGroups[] = $field['customer_group_id'];
            }
        }
 
           
        $customerGroups = $this->_customerGroup->toOptionArray();

        foreach ($customerGroups as $key => $value) {
            if (in_array($value['value'], $membershipCustomerGroups)) {
                unset($customerGroups[$key]);
            }
        }

        return $customerGroups;
    }
}
