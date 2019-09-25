<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\ExtendedZone\Form\Modifier;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneController;
use MageWorx\ShippingRules\Model\ExtendedZoneFactory;

/**
 * Data provider for main panel
 */
class General extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';
    const GENERAL_FIELDSET_NAME = 'general';
    const FIELD_ID_NAME = 'entity_id';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param ExtendedZoneFactory $zoneFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        ExtendedZoneFactory $zoneFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        RequestInterface $request
    ) {
        parent::__construct($arrayManager, $urlBuilder, $zoneFactory, $coreRegistry, $storeManager);
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        // Add submit (save) url to the config
        $actionParameters = [];
        $submitUrl = $this->urlBuilder->getUrl('mageworx_shippingrules/shippingrules_extendedzone/save', $actionParameters);
        $data = array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ],
            ]
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addIdFieldConfig();

        return $this->meta;
    }

    protected function addIdFieldConfig()
    {
        $result[static::GENERAL_FIELDSET_NAME]['children'][static::FIELD_ID_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Zone Id'),
                        'componentType' => Field::NAME,
                        'formElement' => Hidden::NAME,
                        'dataScope' => static::FIELD_ID_NAME,
                        'dataType' => Number::NAME,
                        'sortOrder' => 0,
                        'value' => $this->request->getParam('entity_id'),
                    ],
                ],
            ],
        ];

        $this->meta = array_replace_recursive(
            $this->meta,
            $result
        );
    }
}
