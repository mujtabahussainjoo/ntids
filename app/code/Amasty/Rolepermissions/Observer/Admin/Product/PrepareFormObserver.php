<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class PrepareFormObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::product_owner')) {
            foreach ($observer->getForm()->getElements() as $element) {
                $element->removeField('amrolepermissions_owner');
            }
        }
    }
}
