<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\User\Model\ResourceModel\User;

class CollectionPlugin
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function beforeLoad(
        \Magento\User\Model\ResourceModel\User\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        $currentRule = $this->helper->currentRule();
        if (!$this->registry->registry('its_amrolepermissions')
            && $currentRule
            && $currentRule->getRoles()
        ) {
            $allowedUsers = $currentRule->getAllowedUsers();
            $subject->addFieldToFilter('user_id', ['in' => $allowedUsers]);
        }

        return [$printQuery, $logQuery];
    }
}
