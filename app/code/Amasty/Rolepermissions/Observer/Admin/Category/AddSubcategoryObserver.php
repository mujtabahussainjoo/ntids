<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class AddSubcategoryObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->_authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $allow = $this->_authorization
            ->isAllowed('Amasty_Rolepermissions::create_categories');

        $category = $observer->getCategory();

        if ($category && !$category->getId()) {
            $category->setIsReadonly(!$allow);
        }

        if (!$allow) {
            $observer->getOptions()->setIsAllow(false);
        }
    }
}
