<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\User\Model\ResourceModel;

class UserPlugin
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

    /**
     * @param \Magento\User\Model\ResourceModel\User $subject
     * @param \Magento\User\Model\User $object
     *
     * @return array
     */
    public function beforeSave(
        \Magento\User\Model\ResourceModel\User $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        /** @var \Amasty\Rolepermissions\Model\Rule $currentRule */
        $currentRule = $this->helper->currentRule();
        /**
         * getReloadAclFlag fix save user on user Authentication @see \Magento\Backend\Model\Auth\Session::refreshAcl
         */
        if ($object->getReloadAclFlag()
            && !$this->registry->registry('its_amrolepermissions')
            && $object->getId()
            && $currentRule
            && $currentRule->getRoles()
            && !in_array($object->getId(), $currentRule->getAllowedUsers())
        ) {
            $this->helper->redirectHome();
        }

        return [$object];
    }
}
