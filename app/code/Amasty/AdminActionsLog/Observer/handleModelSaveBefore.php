<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Observer;

use Magento\Framework\Event\ObserverInterface;

class handleModelSaveBefore implements ObserverInterface
{
    protected $_objectManager;
    protected $_helper;
    protected $_appState;
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        \Magento\Framework\App\State $appState
    ) {
        $this->_objectManager = $objectManager;
        $this->_registryManager = $coreRegistry;
        $this->_helper = $helper;
        $this->_appState = $appState;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
                $object = $observer->getObject();

                if ($this->_helper->needToSave($object)) {
                    $this->_saveOldData($object);
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            // no action is Area Code in not set
        }
    }

    protected function _saveOldData($object)
    {
        if ($this->_helper->needOldData($object)) {
            if ($this->_needLoadModel($object)) {
                $class = get_class($object);
                $entity = $this->_objectManager->get($class)->load($object->getId());
                $this->_registryManager->register('amaudit_data_before', $entity->getData(), true);
            } else {
                $data = $object->getData();
                if ($object instanceof \Magento\Catalog\Model\Product) {
                    $data = $this->_helper->_prepareProductData($object);
                }
                $this->_registryManager->register('amaudit_data_before', $data, true);
                if ($object instanceof \Magento\Catalog\Model\Product\Interceptor && !empty($options = $object->getOptions())) {
                    $this->_registryManager->register('amaudit_product_options_before', $object->getOptions(), true);
                }
            }
        }
    }

    protected function _needLoadModel($object)
    {
        $needLoadModel = false;

        $needLoadModelArray = [
            'Magento\Customer\Model\Backend\Customer',
        ];

        foreach ($needLoadModelArray as $class) {
            if (is_a($object, $class)) {
                $needLoadModel = true;
            }
        }

        return $needLoadModel;
    }
}
