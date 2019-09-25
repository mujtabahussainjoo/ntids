<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin;

class StoreManager
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    private $checkForSingleStore = false;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->appState = $appState;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
    }

    public function aroundGetStores(
        \Magento\Store\Model\StoreManager $subject,
        \Closure $proceed,
        $withDefault = false,
        $codeKey = false
    ) {
        $rule = $this->registry->registry('current_amrolepermissions_rule');

        if ($rule) {
            $this->helper = $this->objectManager->get('Amasty\Rolepermissions\Helper\Data');
        }

        if (!$rule
            || $this->checkForSingleStore
            || ($this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
            || $this->helper->canSkipObjectRestriction()
        ) {
            return $proceed($withDefault, $codeKey);
        }

        $allowedStores = $rule->getScopeStoreviews();

        if ($allowedStores) {
            $withDefault = false;
        }

        $result = $proceed($withDefault, $codeKey);

        if ($allowedStores) {
            foreach ($result as $key => $store) {
                if (!in_array($store->getId(), $allowedStores)) {
                    unset($result[$key]);
                }
            }
        }

        reset($result);

        return $result;
    }

    public function aroundGetWebsites(
        \Magento\Store\Model\StoreManager $subject,
        \Closure $proceed,
        $withDefault = false,
        $codeKey = false
    ) {
        $rule = $this->registry->registry('current_amrolepermissions_rule');

        if ($rule) {
            $this->helper = $this->objectManager->get('Amasty\Rolepermissions\Helper\Data');
        }

        if (!$rule
            || $this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
            || $this->helper->canSkipObjectRestriction()
        ) {
            return $proceed($withDefault, $codeKey);
        }

        $allowedStores = $rule->getScopeStoreviews();

        if ($allowedStores) {
            $withDefault = false;
        }

        $result = $proceed($withDefault, $codeKey);

        if ($allowedStores) {
            $allowedWebsites = $rule->getPartiallyAccessibleWebsites();
            foreach ($result as $key => $website) {
                if (!in_array($website->getId(), $allowedWebsites)) {
                    unset($result[$key]);
                }
            }
        }

        reset($result);

        return $result;
    }

    public function aroundHasSingleStore(
        \Magento\Store\Model\StoreManager $subject,
        \Closure $proceed
    ) {
        $this->checkForSingleStore = true;
        $result = $proceed();
        $this->checkForSingleStore = false;

        return $result;
    }
}
