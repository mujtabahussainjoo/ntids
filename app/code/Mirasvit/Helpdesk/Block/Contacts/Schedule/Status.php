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



namespace Mirasvit\Helpdesk\Block\Contacts\Schedule;

use Magento\Framework\DataObject\IdentityInterface;

/**
 *
 */
class Status extends \Mirasvit\Helpdesk\Block\Contacts\Schedule implements IdentityInterface
{
    /**
     * Cache group Tag
     */
    const CACHE_GROUP = 'helpdesk_schedule_block';

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getCurrentSchedule()) {
            return "";
        }

        if ($this->config->getScheduleIsShowStatusOnContactUs()) {
            if ($this->getPage() == 'contact-us') {
                return parent::_toHtml();
            }
        }
        if ($this->config->getScheduleIsShowStatusOnMyTickets()) {
            if ($this->getPage() == 'my-tickets') {
                return parent::_toHtml();
            }
        }
        if ($this->config->getScheduleIsShowStatusOnFeedbackPopup()) {
            if ($this->getPage() == 'feedback-popup') {
                return parent::_toHtml();
            }
        }
        return '';
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_GROUP];
    }
}
