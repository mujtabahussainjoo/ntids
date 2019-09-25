<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;


class handleModelDeleteBefore implements ObserverInterface
{
    protected $_objectManager;
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registryManager = isset($data['registry']) ? $data['registry'] : $coreRegistry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $object = $observer->getObject();
        $id = $object->getEntityId();
        $class = get_class($object);
        $entity = $this->_objectManager->get($class)->load($id);
        $this->_registryManager->register('amaudit_entity_before_delete', $entity, true);
    }
}
