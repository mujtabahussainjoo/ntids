<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;
use Amasty\CronScheduler\Model\Jobs;

class Status implements ArrayInterface
{
    public function toOptionArray()
    {
        $statuses = [
            [
                'value' => Jobs::STATUS_DISABLED, 'label' => '<span class="grid-severity-critical">'
                . htmlspecialchars(__("Disabled"))
                . '</span>'
            ],
            [
                'value' => Jobs::STATUS_ENABLED, 'label' => '<span class="grid-severity-notice">'
                . htmlspecialchars(__("Enabled"))
                . '</span>'
            ]
        ];

        return $statuses;
    }
}
