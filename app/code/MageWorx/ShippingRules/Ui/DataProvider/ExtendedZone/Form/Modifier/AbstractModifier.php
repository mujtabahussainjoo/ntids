<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\ExtendedZone\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Model\ExtendedZone;
use MageWorx\ShippingRules\Model\ExtendedZoneFactory;

/**
 * Class AbstractModifier
 */
abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'mageworx_shippingrules_extendedzone_form';
    const DATA_SOURCE_DEFAULT = 'extendedzone';
    const DATA_SCOPE_EXTENDED_ZONE = 'data.extendedzone';

    /**
     * Container fieldset prefix
     */
    const CONTAINER_PREFIX = 'container_';

    /**
     * Meta config path
     */
    const META_CONFIG_PATH = '/arguments/data/config';

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ExtendedZoneFactory
     */
    protected $zoneFactory;

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
     * @param ExtendedZoneFactory $zoneFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        ExtendedZoneFactory $zoneFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->zoneFactory = $zoneFactory;
        $this->registry = $coreRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * Get current zone or empty
     *
     * @return \MageWorx\ShippingRules\Model\ExtendedZone
     */
    protected function getZone()
    {
        $registry = $this->registry;
        $zone = $registry->registry(ExtendedZone::REGISTRY_KEY);
        if (!$zone) {
            $zone = $this->zoneFactory->create();
        }

        return $zone;
    }
}
