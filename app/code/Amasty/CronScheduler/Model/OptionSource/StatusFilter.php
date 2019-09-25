<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;
use Amasty\CronScheduler\Model\Jobs;

class StatusFilter implements ArrayInterface
{
    public function toOptionArray()
    {
        $statuses = [
            [
                'value' => Jobs::STATUS_DISABLED,
                'label' => __('Disabled')
            ],
            [
                'value' => Jobs::STATUS_ENABLED,
                'label' => __('Enabled')
            ]
        ];

        return $statuses;
    }
}
