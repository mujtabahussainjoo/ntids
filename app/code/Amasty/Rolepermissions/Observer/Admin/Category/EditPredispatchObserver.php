<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class EditPredispatchObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    public function __construct(\Amasty\Rolepermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->helper->currentRule();
        $catRestrictions = $rule->getCategories();

        if (!$catRestrictions) {
            return;
        }

        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        $id = $request->getParam('id');

        if (!$id) { // New category
            $id = $request->getParam('parent'); // Check for parent permissions
        }

        if (!$id || !in_array($id, $catRestrictions)) {
            $id = $catRestrictions[0];

            $request->setParam('id', $id);
        }
    }
}
