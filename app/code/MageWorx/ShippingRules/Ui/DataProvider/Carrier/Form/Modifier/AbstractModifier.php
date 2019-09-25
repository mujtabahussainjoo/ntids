<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\Carrier\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Model\Carrier;
use MageWorx\ShippingRules\Model\CarrierFactory;

/**
 * Class AbstractModifier
 *
 */
abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'mageworx_shippingrules_carrier_form';
    const DATA_SOURCE_DEFAULT = 'carrier';
    const DATA_SCOPE_CARRIER = 'data.carrier';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * @var CarrierFactory
     */
    protected $carrierFactory;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CarrierFactory $carrierFactory
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CarrierFactory $carrierFactory
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->registry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->carrierFactory = $carrierFactory;
    }

    /**
     * Get current carrier
     *
     * @return Carrier|null
     */
    protected function getCarrier()
    {
        $registry = $this->registry;
        $carrier = $registry->registry(Carrier::CURRENT_CARRIER);
        if (!$carrier) {
            $carrier = $this->carrierFactory->create();
            $registry->register(Carrier::CURRENT_CARRIER, $carrier, true);
        }

        return $carrier;
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    protected function getBaseCurrencySymbol()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }
}
