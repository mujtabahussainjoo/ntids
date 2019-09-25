<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Rule;

use Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Attributes;
use Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Categories;
use Magento\Framework\Event\ObserverInterface;

use \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Products;

class SaveObserver implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\Rolepermissions\Model\RuleFactory
     */
    private $ruleFactory;

    public function __construct(
        \Amasty\Rolepermissions\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->coreRegistry = $registry;
        $this->ruleFactory = $ruleFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $role = $this->coreRegistry->registry('current_role');

        if (!$role->getId()) {
            return;
        }

        $request = $observer->getRequest();
        $data = $request->getParam('amrolepermissions');
        if (!$data) {
            return;
        }
        /** @var  \Amasty\Rolepermissions\Model\Rule $rule */
        $rule = $this->ruleFactory->create();
        $rule = $rule->load($role->getId(), 'role_id');

        $rule->setScopeWebsites([])
            ->setScopeStoreviews([]);

        $data['role_id'] = $role->getId();

        if (isset($data['product_access_mode'])) {
            switch ($data['product_access_mode']) {
                case Products::MODE_ANY:
                case Products::MODE_MY:
                case Products::MODE_SCOPE:
                    $data['products'] = [];
                    break;
                case Products::MODE_SELECTED:
                    $data['products'] = explode('&', $data['products']);
                    break;
            }
        }

        if (isset($data['attribute_access_mode'])) {
            switch ($data['attribute_access_mode']) {
                case Attributes::MODE_ANY:
                    $data['attributes'] = [];
                    break;
                case Attributes::MODE_SELECTED:
                    $data['attributes'] = explode('&', $data['attributes']);
                    break;
            }
        }

        if (isset($data['category_access_mode'])) {
            switch ($data['category_access_mode']) {
                case Categories::MODE_ALL:
                    $data['categories'] = [];
                    break;
                case Categories::MODE_SELECTED:
                    $data['categories'] = explode(',', str_replace(' ', '', $data['categories']));
                    break;
            }
        }

        if (isset($data['role_access_mode'])) {
            switch ($data['role_access_mode']) {
                case Attributes::MODE_ANY:
                    $data['roles'] = [];
                    break;
                case Attributes::MODE_SELECTED:
                    $data['roles'] = explode('&', $data['roles']);
                    break;
            }
        }

        $rule->addData($data);

        $rule->save();
    }
}
