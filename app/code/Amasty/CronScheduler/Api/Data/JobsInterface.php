<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Api\Data;

interface JobsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';

    const CODE = 'code';

    const GROUP = 'group';

    const INSTANCE = 'instance';

    const METHOD = 'method';

    const SCHEDULE = 'schedule';

    const MODIFIED_SCHEDULE = 'modified_schedule';

    const STATUS = 'status';

    const FAIL_TIME = 'fail_time';

    const WAS_NOTIFIED = 'was_ntofied';

    /**#@-*/
    /**
     * @return string
     */
    public function getJobCode();

    /**
     * @param string $code
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setJobCode($code);

    /**
     * @return string
     */
    public function getGroup();

    /**
     * @param string $group
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setGroup($group);

    /**
     * @return string
     */
    public function getInstance();

    /**
     * @param string $instance
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setInstance($instance);

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @param string $method
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setMethod($method);

    /**
     * @return string
     */
    public function getSchedule();

    /**
     * @param string $schedule
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setSchedule($schedule);

    /**
     * @return string
     */
    public function getModifiedSchedule();

    /**
     * @param string $modifiedSchedule
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setModifiedSchedule($modifiedSchedule);

    /**
     * @return bool
     */
    public function getStatus();

    /**
     * @param bool $status
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getFailTime();

    /**
     * @param string $failTime
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setFailTime($failTime);

    /**
     * @return bool
     */
    public function getWasNotified();

    /**
     * @param bool $wasNotified
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function setWasNotified($wasNotified);
}
