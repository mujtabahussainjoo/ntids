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



namespace Mirasvit\Helpdesk\Helper;

use Mirasvit\Helpdesk\Model\Config as Config;

class Channel extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function getLabel($code)
    {
        $channels = [
            Config::CHANNEL_FEEDBACK_TAB => 'Feedback Tab',
            Config::CHANNEL_CONTACT_FORM => 'Contact Form',
            Config::CHANNEL_CUSTOMER_ACCOUNT => 'Customer Account',
            Config::CHANNEL_EMAIL => 'Email',
            Config::CHANNEL_BACKEND => 'Backend',
        ];
        if (isset($channels[$code])) {
            return $channels[$code];
        }
    }
}
