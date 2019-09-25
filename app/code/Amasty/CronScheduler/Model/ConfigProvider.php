<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    const PATH_PREFIX = 'amasty_cronscheduler/';

    /**#@+
     * Constants defined for xpath of system configuration
     */
    const ENABLED = 'email_notification/enabled';

    const SENDER_EMAIL = 'email_notification/sender_email';

    const SEND_TO = 'email_notification/send_to';

    const EMAIL_SUBJECT = 'email_notification/email_subject';

    const EMAIL_CONTENT = 'email_notification/email_content';

    const NOTIFICATION_INTERVAL = 'email_notification/notification_interval';

    /**#@-*/

    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * @param null|string $scopeCode
     *
     * @return bool
     */
    public function getIsEnabled($scopeCode = null)
    {
        return (bool)$this->getValue(self::ENABLED, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return string
     */
    public function getSenderEmail($scopeCode = null)
    {
        return $this->getValue(self::SENDER_EMAIL, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return string
     */
    public function getRecievers($scopeCode = null)
    {
        return $this->getValue(self::SEND_TO, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return string
     */
    public function getEmailSubject($scopeCode = null)
    {
        return $this->getValue(self::EMAIL_SUBJECT, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return string
     */
    public function getEmailContent($scopeCode = null)
    {
        return $this->getValue(self::EMAIL_CONTENT, $scopeCode);
    }

    /**
     * @param null|string $scopeCode
     *
     * @return string
     */
    public function getNotificationInterval($scopeCode = null)
    {
        return $this->getValue(self::NOTIFICATION_INTERVAL, $scopeCode);
    }
}
