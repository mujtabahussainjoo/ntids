<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Amasty\CronScheduler\Api\Data\JobsInterface;

class CreateJobsTable
{
    const TABLE_NAME = 'amasty_cronscheduler_jobs';

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws \Zend_Db_Exception
     */
    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Cron Scheduler Table for jobs'
            )->addColumn(
                JobsInterface::ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'ID'
            )->addColumn(
                JobsInterface::CODE,
                Table::TYPE_TEXT,
                225,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Job Code'
            )->addColumn(
                JobsInterface::GROUP,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Group'
            )->addColumn(
                JobsInterface::INSTANCE,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Instance'
            )->addColumn(
                JobsInterface::METHOD,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Method'
            )->addColumn(
                JobsInterface::SCHEDULE,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Schedule'
            )->addColumn(
                JobsInterface::MODIFIED_SCHEDULE,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false
                ],
                'Modified Schedule'
            )->addColumn(
                JobsInterface::STATUS,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false
                ],
                'Status'
            )->addColumn(
                JobsInterface::FAIL_TIME,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => true
                ],
                'Last error timestamp'
            )->addColumn(
                JobsInterface::WAS_NOTIFIED,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => true
                ],
                'Is error notification sent'
            )->addIndex(
                $setup->getIdxName(
                    self::TABLE_NAME,
                    [JobsInterface::CODE],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [JobsInterface::CODE],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );
    }

}
