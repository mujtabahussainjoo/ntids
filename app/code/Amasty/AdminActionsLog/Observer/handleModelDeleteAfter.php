<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;


class handleModelDeleteAfter implements ObserverInterface
{
    protected $_objectManager;
    protected $_helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\AdminActionsLog\Helper\Data $helper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helper->needToSave($observer->getObject())) {
            $object = $observer->getObject();
            /** @var \Amasty\AdminActionsLog\Model\Log $logModel */
            $logModel = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
            $data = $logModel->prepareLogData($object);
            if (!isset($data['username'])) {
                return;
            }
            $logModel->setData($data);
            $logModel->save();
        }
    }
}
