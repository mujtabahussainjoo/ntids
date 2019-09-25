<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Api;

interface JobsRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\CronScheduler\Api\Data\JobsInterface $job
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     */
    public function save(\Amasty\CronScheduler\Api\Data\JobsInterface $job);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\CronScheduler\Api\Data\JobsInterface $job
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\CronScheduler\Api\Data\JobsInterface $job);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id);

    /**
     * Get job by code
     *
     * @param int $code
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code);

    /**
     * Get all jobs
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface[]
     */
    public function getAll();

    /**
     * Get all failed jobs
     *
     * @return \Amasty\CronScheduler\Api\Data\JobsInterface[]
     */
    public function getAllFailed();
}
