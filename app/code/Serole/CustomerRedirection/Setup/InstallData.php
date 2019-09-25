<?php

namespace Serole\CustomerRedirection\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Sales\Setup\SalesSetupFactory;


class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    private $salesSetupFactory;

    private $customerSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory,
                                Config $eavConfig,
                                SalesSetupFactory $salesSetupFactory,
                                \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);


        $used_in_forms[]="adminhtml_customer";
        $used_in_forms[]="checkout_register";
        $used_in_forms[]="customer_account_create";
        $used_in_forms[]="customer_account_edit";
        $used_in_forms[]="adminhtml_checkout";

        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'block_fraud_customer');
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'block_fraud_customer',
            [
                'type' => 'int',
                'label' => 'block_fraud_customer',
                'input' => 'boolean',
                'source' => '',
                'required' => false,
                "visible"  => true,
                'default' => '',
                "frontend" => "",
                'sort_order' => 410,
                'system' => false,
                'position' => 410
            ]
        );
        $block_fraud_customer = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'block_fraud_customer');
        $block_fraud_customer->setData("used_in_forms", $used_in_forms)
                            ->setData("is_used_for_customer_segment", true)
                            ->setData("is_system", 0)
                            ->setData("is_user_defined", 1)
                            ->setData("is_visible", 1)
                            ->setData("sort_order", 410);
        $block_fraud_customer->save();



        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'is_suspended');
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'is_suspended',
            [
                'type' => 'int',
                'label' => 'is_suspended',
                'input' => 'boolean',
                'source' => '',
                'required' => false,
                "visible"  => true,
                'default' => '',
                "frontend" => "",
                'sort_order' => 411,
                'system' => false,
                'position' => 411
            ]
        );
        $is_suspended = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'is_suspended');
        $is_suspended->setData("used_in_forms", $used_in_forms)
                     ->setData("is_used_for_customer_segment", true)
                     ->setData("is_system", 0)
                     ->setData("is_user_defined", 1)
                     ->setData("is_visible", 1)
                     ->setData("sort_order", 411);
        $is_suspended->save();



        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ssoid');
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ssoid',
            [
                'type' => 'varchar',
                'label' => 'ssoid',
                'input' => 'text',
                'source' => '',
                'required' => false,
                "visible"  => true,
                'default' => '',
                "frontend" => "",
                'sort_order' => 412,
                'system' => false,
                'position' => 412
            ]
        );

        $ssoid = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'ssoid');
        $ssoid->setData("used_in_forms", $used_in_forms)
                    ->setData("is_used_for_customer_segment", true)
                    ->setData("is_system", 0)
                    ->setData("is_user_defined", 1)
                    ->setData("is_visible", 1)
                    ->setData("sort_order", 412);
        $ssoid->save();


        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'gigya_uid');
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'gigya_uid',
            [
                'type' => 'varchar',
                'label' => 'gigya_uid',
                'input' => 'text',
                'source' => '',
                'required' => false,
                "visible"  => true,
                'default' => '',
                "frontend" => "",
                'sort_order' => 413,
                'system' => false,
                'position' => 413
            ]
        );
        $gigya_uid = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'gigya_uid');
        $gigya_uid->setData("used_in_forms", $used_in_forms)
                    ->setData("is_used_for_customer_segment", true)
                    ->setData("is_system", 0)
                    ->setData("is_user_defined", 1)
                    ->setData("is_visible", 1)
                    ->setData("sort_order", 413);
        $gigya_uid->save();

    }
}