<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model\Source;

class Users implements \Magento\Framework\Option\ArrayInterface
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    public function toOptionArray()
    {
        $values = [];

        $adminUsersCollection = $this->_objectManager->get('Magento\User\Model\User')->getCollection();
        foreach ($adminUsersCollection as $admin) {
            $values[] = ['value' => $admin->getUserId(),
                'label' => $admin->getFirstname() . ' ' .  $admin->getLastname() . ' (' . $admin->getUsername() . ')'];
        }
        return $values;
    }
}
