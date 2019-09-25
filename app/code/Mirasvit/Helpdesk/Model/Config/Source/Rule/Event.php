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



namespace Mirasvit\Helpdesk\Model\Config\Source\Rule;

use Mirasvit\Helpdesk\Model\Config as Config;

class Event implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Config::RULE_EVENT_NEW_TICKET              => __('New ticket created'),
            Config::RULE_EVENT_NEW_CUSTOMER_REPLY      => __('New reply from customer'),
            Config::RULE_EVENT_NEW_STAFF_REPLY         => __('New reply from staff'),
            Config::RULE_EVENT_NEW_THIRD_REPLY         => __('New reply from third party'),
            Config::RULE_EVENT_TICKET_ASSIGNED         => __('Ticket assigned to staff'),
            Config::RULE_EVENT_TICKET_UPDATED          => __('Ticket was changed'),
            Config::RULE_EVENT_TICKET_CONVERTED_TO_RMA => __('Ticket was converted to RMA'),
            Config::RULE_EVENT_CRON_EVERY_HOUR         => __('Check every hour'),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /************************/
}
