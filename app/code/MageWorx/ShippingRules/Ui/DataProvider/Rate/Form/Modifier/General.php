<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Ui\DataProvider\Rate\Form\Modifier;

use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate as RateController;
use MageWorx\ShippingRules\Model\ResourceModel\Method\CollectionFactory as MethodCollectionFactory;
use MageWorx\ShippingRules\Model\Carrier\Method\RateFactory;

/**
 * Data provider for main panel
 */
class General extends AbstractModifier
{
    const KEY_SUBMIT_URL = 'submit_url';

    const GENERAL_FIELDSET_NAME = 'general';
    const FIELD_METHOD_ID_NAME = 'method_id';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var MethodCollectionFactory
     */
    protected $methodCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param RateFactory $rateFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param MethodCollectionFactory $methodCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        RateFactory $rateFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        MethodCollectionFactory $methodCollectionFactory,
        RequestInterface $request
    ) {
        parent::__construct($arrayManager, $urlBuilder, $rateFactory, $coreRegistry, $storeManager);
        $this->methodCollectionFactory = $methodCollectionFactory;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        // Add submit (save) url to the config
        $actionParameters = [];
        $submitUrl = $this->urlBuilder->getUrl('mageworx_shippingrules/shippingrules_rate/save', $actionParameters);
        $data = array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
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
        $this->addMethodFieldConfig();

        return $this->meta;
    }

    /**
     * Add methods input:
     * hidden for existing rate and select with all available methods for the new rate
     * hidden input for the 'back_to' param (if need to redirect admin back to the method edit form page)
     */
    protected function addMethodFieldConfig()
    {
        // Method id form field (hidden for existing rate or select for the new rate)
        $rate = $this->getRate();
        if ($rate->getData('method_id')) {
            $methodFieldConfig = [
                'label' => __('Method'),
                'componentType' => Field::NAME,
                'formElement' => Hidden::NAME,
                'dataScope' => static::FIELD_METHOD_ID_NAME,
                'dataType' => Number::NAME,
                'sortOrder' => 0,
            ];
        } elseif ($this->request->getParam('method_id')) {
            $methodFieldConfig = [
                'label' => __('Method'),
                'componentType' => Field::NAME,
                'formElement' => Hidden::NAME,
                'dataScope' => static::FIELD_METHOD_ID_NAME,
                'dataType' => Number::NAME,
                'sortOrder' => 0,
                'value' => $this->request->getParam('method_id')
            ];
        } else {
            $methodFieldConfig = [
                'label' => __('Method'),
                'componentType' => Field::NAME,
                'formElement' => Select::NAME,
                'dataScope' => static::FIELD_METHOD_ID_NAME,
                'dataType' => Text::NAME,
                'sortOrder' => 0,
                'options' => $this->getMethods(),
                'disableLabel' => true,
                'multiple' => false,
                'validation' => [
                    'required-entry' => true
                ]
            ];
        }

        $result[static::GENERAL_FIELDSET_NAME]['children'][static::FIELD_METHOD_ID_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => $methodFieldConfig
                ],
            ],
        ];

        // The "back_to" hidden input, if need to redirect admin back to the method edit form
        if ($this->request->getParam(RateController::BACK_TO_PARAM)) {
            $result[static::GENERAL_FIELDSET_NAME]['children'][RateController::BACK_TO_PARAM] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => '',
                            'componentType' => Field::NAME,
                            'formElement' => Hidden::NAME,
                            'dataScope' => RateController::BACK_TO_PARAM,
                            'dataType' => Number::NAME,
                            'sortOrder' => 0,
                            'value' => $this->request->getParam(RateController::BACK_TO_PARAM)
                        ]
                    ],
                ],
            ];
        }

        $this->meta = array_replace_recursive(
            $this->meta,
            $result
        );
    }

    /**
     * Get the methods collection as an option array
     *
     * @return array
     */
    protected function getMethods()
    {
        /** @var \MageWorx\ShippingRules\Model\ResourceModel\Method\Collection $methodCollection */
        $methodCollection = $this->methodCollectionFactory->create();
        $result = $methodCollection->toOptionArray('entity_id', 'title', [], true);

        return $result;
    }
}
