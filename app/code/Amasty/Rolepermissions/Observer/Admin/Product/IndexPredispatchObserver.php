<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class IndexPredispatchObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data $helper
     */
    private $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $messageManager
     */
    private $messageManager;

    /**
     * IndexPredispatchObserver constructor.
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (empty($this->helper->getAllowedSetIds())) {
            $this->messageManager->addWarningMessage(__('Attributes required to create a product are restricted'));
        }
    }
}
