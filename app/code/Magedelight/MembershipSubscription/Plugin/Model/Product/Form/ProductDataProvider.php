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

namespace Magedelight\MembershipSubscription\Plugin\Model\Product\Form;

class ProductDataProvider
{

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
     * @param \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param array $data
     */
    public function __construct(
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
    
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->request = $request;
    }
    
    
    /**
     *
     * @param \Magento\Customer\Model\Customer\DataProvider $provider
     * @param type $result
     * @return boolean
     */
    public function afterGetData(\Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider $provider, $result)
    {
        $requestId = $this->request->getParam('id');

        if ((!empty($requestId)) && (count($result)>0)) {
            $productType = "";
            if(isset($result[$requestId]['product']['stock_data']['type_id']))
            {
                $productType = $result[$requestId]['product']['stock_data']['type_id'];
            }
            
            if (isset($productType) && $productType == "Membership") {
                $model = $this->_MembershipProductsFactory->create();
                $model->load($requestId, 'product_id');
                $data = $model->getData();
                
                if (count($data)>0) {
                    $membership_duration = unserialize($data['membership_duration']);
                    $featured = $data['featured'];

                    $result[$requestId]['product']['membership_duration'] = $membership_duration;
                    $result[$requestId]['product']['featured'] = $featured;
                    
                    return $result;
                } else {
                    return $result;
                }
            } else {
                return $result;
            }
        } else {
            return $result;
        }
    }
}
