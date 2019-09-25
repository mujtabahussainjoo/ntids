<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Block\Adminhtml\Store;

class Switcher
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    public function __construct(\Amasty\Rolepermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetStoreIds(\Magento\Backend\Block\Store\Switcher $subject, $result)
    {
        $allowedStores = $this->helper->currentRule()->getScopeStoreviews();

        if (!is_array($allowedStores) || empty($allowedStores)) {
            return $result;
        }

        if (is_array($result) && !empty($result)) {
            return array_intersect($result, $allowedStores);
        } else {
            return $allowedStores;
        }
    }

    public function afterHasDefaultOption(\Magento\Backend\Block\Store\Switcher $subject, $result)
    {
        $allowedStores = $this->helper->currentRule()->getScopeStoreviews();

        if (!is_array($allowedStores) || empty($allowedStores)) {
            return $result;
        } else {
            return false;
        }
    }

    public function afterIsWebsiteSwitchEnabled(\Magento\Backend\Block\Store\Switcher $subject, $result)
    {
        $rule = $this->helper->currentRule();
        $allowedStores = $rule->getScopeStoreviews();

        if ($allowedStores && !$rule->getScopeWebsites()) {
            return false;
        } else {
            return $result;
        }
    }
}
