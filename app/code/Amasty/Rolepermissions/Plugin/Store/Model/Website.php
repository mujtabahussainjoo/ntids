<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

namespace Amasty\Rolepermissions\Plugin\Store\Model;

class Website
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Website constructor.
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Store\Model\Website $subject
     */
    public function beforeGetDefaultStore($subject)
    {
        $this->registry->register('am_dont_change_collection', true, true);
    }
}
