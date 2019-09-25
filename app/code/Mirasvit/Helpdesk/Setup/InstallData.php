<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Setup;

use Mirasvit\Helpdesk\Api\Data\StatusInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->installDepartments($setup);
        $this->installProperties($setup);
        $this->installStatuses($setup);
        $this->installPermissions($setup);

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function installPermissions(ModuleDataSetupInterface $setup)
    {
        $data = [
            [
                'role_id' => new \Zend_Db_Expr('NULL'),
                'is_ticket_remove_allowed' => 1,
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_permission'), $row);
        }
        $data = [
            [
                'permission_id' => 1,
                'department_id' => new \Zend_Db_Expr('NULL'),
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_permission_department'), $row);
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function installStatuses(ModuleDataSetupInterface $setup)
    {
        $data = [
            [
                'status_id' => StatusInterface::OPEN,
                'name' => 'Open',
                'code' => 'open',
                'sort_order' => 10,
                'color' => 'green',
            ],
            [
                'status_id' => StatusInterface::IN_PROGRESS,
                'name' => 'In Progress',
                'code' => 'in_progress',
                'sort_order' => 20,
                'color' => 'yellow',
            ],
            [
                'status_id' => StatusInterface::CLOSED,
                'name' => 'Closed',
                'code' => 'closed',
                'sort_order' => 30,
                'color' => 'black',
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_status'), $row);
        }
        $data = [
            ['ss_status_id' => 1],
            ['ss_status_id' => 2],
            ['ss_status_id' => 3],
        ];
        $stores = $this->getStores($setup);
        foreach ($data as $row) {
            foreach ($stores as $store) {
                $row['ss_store_id'] = $store['store_id'];
                $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_status_store'), $row);
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function installProperties(ModuleDataSetupInterface $setup)
    {
        $data = [
            [
                'priority_id' => 1,
                'name' => 'High',
                'sort_order' => 30,
                'color' => 'red',
            ],
            [
                'priority_id' => 2,
                'name' => 'Medium',
                'sort_order' => 20,
                'color' => 'yellow',
            ],
            [
                'priority_id' => 3,
                'name' => 'Low',
                'sort_order' => 10,
                'color' => 'blue',
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_priority'), $row);
        }
        $data = [
            ['ps_priority_id' => 1],
            ['ps_priority_id' => 2],
            ['ps_priority_id' => 3],
        ];
        $stores = $this->getStores($setup);
        foreach ($data as $row) {
            foreach ($stores as $store) {
                $row['ps_store_id'] = $store['store_id'];
                $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_priority_store'), $row);
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    private function installDepartments(ModuleDataSetupInterface $setup)
    {
        $data = [
            [
                'department_id' => 1,
                'name' => 'Sales team',
                'sort_order' => 10,
                'sender_email' => 'sales@example.com',
                'is_notification_enabled' => 1,
                'is_active' => 1,
            ],
            [
                'department_id' => 2,
                'name' => 'Support team',
                'sort_order' => 10,
                'sender_email' => 'support@example.com',
                'is_notification_enabled' => 1,
                'is_active' => 1,
            ],
        ];
        foreach ($data as $department) {
            $setup->getConnection()->insertForce(
                $setup->getTable('mst_helpdesk_department'),
                $department
            );
        }

        $user = $this->getUser($setup);
        $data = [
            ['du_department_id' => 1],
            ['du_department_id' => 2],
        ];
        foreach ($data as $row) {
            $row['du_user_id'] = $user['user_id'];
            $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_department_user'), $row);
        }

        $data = [
            ['ds_department_id' => 1],
            ['ds_department_id' => 2],
        ];
        $stores = $this->getStores($setup);
        foreach ($data as $row) {
            foreach ($stores as $store) {
                $row['ds_store_id'] = $store['store_id'];
                $setup->getConnection()->insertForce($setup->getTable('mst_helpdesk_department_store'), $row);
            }
        }
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return array
     */
    private function getUser(ModuleDataSetupInterface $setup)
    {
        $customerEntityTable = $setup->getTable('admin_user');

        $select = $setup->getConnection()->select()->from(
            $customerEntityTable,
            '*'
        );

        $user = $setup->getConnection()->fetchRow($select);

        if (empty($user['user_id'])) {
            $user['user_id'] = 1;
        }

        return $user;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return array
     */
    private function getStores(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $select = $connection->select()->from(
            $setup->getTable('store'),
            '*'
        )->where(
            'store_id <> ?',
            0
        );

        return $connection->fetchAll($select);
    }
}
