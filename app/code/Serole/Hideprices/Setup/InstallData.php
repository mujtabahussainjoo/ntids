<?php

namespace Serole\Hideprices\Setup;

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

        $eavSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, 'hideprices');
        $eavSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'hideprices',
            [
                'type' => 'int',
                'label' => 'Hide Prices',
                'input' => 'boolean',
                'source' => '',
                'required' => false,
                "visible"  => true,
                'default' => '',
                "frontend" => "",
                'sort_order' => 310,
                'system' => false,
                'position' => 310
            ]
        );

        $hidePricesAttribute = $this->eavConfig->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'hideprices');
        $hidePricesAttribute->setData("used_in_forms", $used_in_forms)
                            ->setData("is_used_for_customer_segment", true)
                            ->setData("is_system", 0)
                            ->setData("is_user_defined", 1)
                            ->setData("is_visible", 1)
                            ->setData("sort_order", 310);
        $hidePricesAttribute->save();

    }
}