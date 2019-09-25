<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model;

use Amasty\CronScheduler\Api\Data\JobsInterface;
use Magento\Framework\Model\AbstractModel;

class Jobs extends AbstractModel implements JobsInterface
{
    /**#@+
     * Constants
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    /**#@-*/

    public function _construct()
    {
        $this->_init(ResourceModel\Jobs::class);
    }

    /**
     * @inheritdoc
     */
    public function getJobCode()
    {
        return $this->_getData(JobsInterface::CODE);
    }

    /**
     * @inheritdoc
     */
    public function setJobCode($code)
    {
        $this->setData(JobsInterface::CODE, $code);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGroup()
    {
        return $this->_getData(JobsInterface::GROUP);
    }

    /**
     * @inheritdoc
     */
    public function setGroup($group)
    {
        $this->setData(JobsInterface::GROUP, $group);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getInstance()
    {
        return $this->_getData(JobsInterface::INSTANCE);
    }

    /**
     * @inheritdoc
     */
    public function setInstance($instance)
    {
        $this->setData(JobsInterface::INSTANCE, $instance);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->_getData(JobsInterface::METHOD);
    }

    /**
     * @inheritdoc
     */
    public function setMethod($method)
    {
        $this->setData(JobsInterface::METHOD, $method);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSchedule()
    {
        return $this->_getData(JobsInterface::SCHEDULE);
    }

    /**
     * @inheritdoc
     */
    public function setSchedule($schedule)
    {
        $this->setData(JobsInterface::SCHEDULE, $schedule);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getModifiedSchedule()
    {
        return $this->_getData(JobsInterface::MODIFIED_SCHEDULE);
    }

    /**
     * @inheritdoc
     */
    public function setModifiedSchedule($modifiedSchedule)
    {
        $this->setData(JobsInterface::MODIFIED_SCHEDULE, $modifiedSchedule);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_getData(JobsInterface::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->setData(JobsInterface::STATUS, $status);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFailTime()
    {
        return $this->_getData(JobsInterface::FAIL_TIME);
    }

    /**
     * @inheritdoc
     */
    public function setFailTime($failTime)
    {
        $this->setData(JobsInterface::FAIL_TIME, $failTime);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getWasNotified()
    {
        return $this->_getData(JobsInterface::WAS_NOTIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setWasNotified($wasNotified)
    {
        $this->setData(JobsInterface::WAS_NOTIFIED, $wasNotified);

        return $this;
    }
}
