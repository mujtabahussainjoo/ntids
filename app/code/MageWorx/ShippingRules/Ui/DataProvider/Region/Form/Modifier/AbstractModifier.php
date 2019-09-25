<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\Region\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use MageWorx\ShippingRules\Model\Region;
use MageWorx\ShippingRules\Model\RegionFactory;

/**
 * Class AbstractModifier
 */
abstract class AbstractModifier implements ModifierInterface
{
    const FORM_NAME = 'mageworx_shippingrules_region_form';
    const DATA_SOURCE_DEFAULT = 'region';
    const DATA_SCOPE_METHOD = 'data.region';

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
     * @var RegionFactory
     */
    protected $regionFactory;

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
     * @param RegionFactory $regionFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        RegionFactory $regionFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->regionFactory = $regionFactory;
        $this->registry = $coreRegistry;
        $this->storeManager = $storeManager;
    }

    /**
     * Get current region or empty
     *
     * @return \MageWorx\ShippingRules\Model\Region
     */
    protected function getRegion()
    {
        $registry = $this->registry;
        $rate = $registry->registry(Region::CURRENT_REGION);
        if (!$rate) {
            $rate = $this->regionFactory->create();
        }

        return $rate;
    }
}
