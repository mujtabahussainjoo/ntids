<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class MassDeletePredispatchObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Amasty\Rolepermissions\Helper\Data $helper
    ) {
        $this->authorization = $authorization;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::delete_products')) {
            $this->helper->redirectHome();
        }

        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        $ids = $request->getParam('selected');
        if (!is_array($ids)) {
            return;
        }

        $rule = $this->helper->currentRule();
        $productRestriction = $rule->getAllowedProductIds(); // allow to delete own products

        if (is_string($productRestriction)) {
            $productRestriction = explode(',', $productRestriction);
        }
        if (!is_array($productRestriction)) {
            return;
        }

        $diff = array_diff($ids, $productRestriction);
        if (!empty($diff)) {
            $this->helper->redirectHome();
        }
    }
}
