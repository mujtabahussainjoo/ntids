<?php

namespace MagePsycho\GroupSwitcherPro\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /* @var customer Magento\Customer\Setup\CustomerSetupFactory */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $attributesInfo = [
            'mp_group_code' => [
                'label'     => 'Group Code',
                'type'      => 'varchar',
                'input'     => 'text',
                'source'    => '',
                'required'  => false,
                'visible'   => true,
                'system'    => false,
                'user_defined'  => true,
                'backend'       => 'MagePsycho\GroupSwitcherPro\Model\Customer\Attribute\Backend\GroupCode',
                'position'  => 100,
            ]
        ];

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');

        /** @var $attributeSet AttributeSet */
        $attributeSet       = $this->attributeSetFactory->create();
        $attributeSetId     = 1; //$customerEntity->getDefaultAttributeSetId();
        $attributeGroupId   = 1; //$attributeSet->getDefaultGroupId($attributeSetId);

        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeParams);
        }

        $groupCodeAttribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'mp_group_code')
        ;
        $groupCodeAttribute->addData([
            'attribute_set_id'      => $attributeSetId,
            'attribute_group_id'    => $attributeGroupId,
            'used_in_forms'         => [
                'customer_account_create',
                'customer_account_edit',
                'adminhtml_customer'
            ],
        ]);
        $groupCodeAttribute->save();

        $groupIdAttribute = $customerSetup->getEavConfig()
            ->getAttribute(Customer::ENTITY, 'group_id')
        ;
        $groupIdAttribute->setData(
            'used_in_forms',
            [
                'customer_account_create',
                'customer_account_edit',
                'adminhtml_customer'
            ]
        );
        $groupIdAttribute->save();

        $setup->endSetup();
    }
}