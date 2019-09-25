<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model\ResourceModel\Jobs;

use Amasty\CronScheduler\Model\Jobs;
use Amasty\CronScheduler\Model\ResourceModel\Jobs as JobsResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @method Jobs[] getItems()
 */
class Collection extends AbstractCollection
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(Jobs::class, JobsResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
