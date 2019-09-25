<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Form\Field;

use Magento\Shipping\Model\Config as ShippingConfig;

class Methods extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var ShippingConfig
     */
    protected $shippingMethodsConfig;

    /**
     * Shipping methods cache
     *
     * @var array
     */
    private $shippingMethods;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        ShippingConfig $shippingConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shippingMethodsConfig = $shippingConfig;
    }

    /**
     * Retrieve allowed shipping methods
     *
     * @param int $methodId return name by shipping method id
     * @return array|string
     */
    protected function _getMethods($methodId = null)
    {
        if ($this->shippingMethods === null) {
            $this->shippingMethods = [];
            $this->shippingMethods = $this->getShippingMethodsList();
        }

        if ($methodId !== null) {
            return isset($this->shippingMethods[$methodId]) ? $this->shippingMethods[$methodId] : null;
        }

        return $this->shippingMethods;
    }

    /**
     * Option array of all shipping methods
     *
     * @param bool $isActiveOnlyFlag
     *
     * @return array
     */
    private function getShippingMethodsList($isActiveOnlyFlag = false)
    {
        $methods = [];
        $carriers = $this->shippingMethodsConfig->getAllCarriers();
        foreach ($carriers as $carrierCode => $carrierModel) {
            if (!$carrierModel->isActive() && (bool)$isActiveOnlyFlag === true) {
                continue;
            }
            $carrierMethods = $carrierModel->getAllowedMethods();
            if (!$carrierMethods) {
                continue;
            }

            foreach ($carrierMethods as $methodCode => $methodTitle) {
                $methods[$carrierCode . '_' . $methodCode] =
                    '[' . $carrierCode . '_' . $methodCode . '] ' . ($methodTitle ? $methodTitle : $methodCode);
            }
        }

        return $methods;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getMethods() as $methodId => $methodLabel) {
                $this->addOption($methodId, addslashes($methodLabel));
            }
        }
        return parent::_toHtml();
    }
}