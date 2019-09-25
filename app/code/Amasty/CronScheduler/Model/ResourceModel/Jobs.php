<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model\ResourceModel;

use Amasty\CronScheduler\Setup\Operation\CreateJobsTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Jobs extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateJobsTable::TABLE_NAME, 'id');
    }
}
