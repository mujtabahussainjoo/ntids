<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class EditPostdispatchObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    private $view;

    /**
     * @var \Amasty\Rolepermissions\Helper\Data $helper
     */
    private $helper;

    /**
     * EditPostdispatchObserver constructor.
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ViewInterface $view,
        \Amasty\Rolepermissions\Helper\Data $helper
    ) {
        $this->view = $view;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->restrictAttributeSets()) {
            $toolbar = $this->view->getLayout()->getBlock('page.actions.toolbar');
            $toolbar->unsetChild('addButton');
        }
    }
}
