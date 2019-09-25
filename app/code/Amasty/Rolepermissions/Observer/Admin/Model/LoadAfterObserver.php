<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;

class LoadAfterObserver implements ObserverInterface
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data\Proxy
     */
    private $helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Amasty\Rolepermissions\Helper\Data\Proxy $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->coreRegistry = $registry;
        $this->helper = $helper;
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->coreRegistry->registry('current_amrolepermissions_rule');

        if (!$rule || $this->request->getModuleName() == 'api') {
            return;
        }

        /** @var AbstractModel $object */
        $object = $observer->getObject();

        if (in_array($this->request->getActionName(), ['edit', 'view']) && $object->getId()) {
            if ($this->helper->canSkipObjectRestriction()) {
                return;
            }

            $idParam = $this->request->getParam('id');
            if ($idParam && $object->getId() != $idParam) {
                return;
            }

            $controllerName = $this->request->getControllerName();
            if ($this->request->getModuleName() == 'sales'
                && !($object instanceof \Magento\Sales\Model\AbstractModel)) {
                return;
            }

            if ($controllerName == 'product' && !($object instanceof \Magento\Catalog\Model\Product)) {
                return;
            }

            if ($controllerName == 'customer' && !($object instanceof \Magento\Customer\Model\Customer)) {
                return;
            }

            if ($rule->getScopeStoreviews()) {
                $this->helper->restrictObjectByStores($object->getData());
            }
        }
    }
}
