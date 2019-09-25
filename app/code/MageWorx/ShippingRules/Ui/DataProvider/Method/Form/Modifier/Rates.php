<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\Method\Form\Modifier;

use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\Phrase;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate as RateController;
use MageWorx\ShippingRules\Model\ResourceModel\Carrier\CollectionFactory as CarrierCollectionFactory;
use MageWorx\ShippingRules\Model\Carrier\MethodFactory;

class Rates extends AbstractModifier
{
    const FIELD_RATES_NAME = 'rate';
    const FIELD_IS_DELETE = 'is_delete';
    const FIELD_SORT_ORDER_NAME = 'sort_order';

    const CONTAINER_HEADER_NAME = 'header';
    const CONTAINER_RATE = 'container_rate';
    const GRID_RATES_NAME = 'rates_listing';

    /** Buttons */
    const BUTTON_ADD = 'button_add';

    /**
     * @var array
     */
    protected $meta;

    /**
     * Url path to add a new rate
     *
     * @var string
     */
    const URL_PATH_NEW = 'mageworx_shippingrules/shippingrules_rate/new';

    /**
     * Url path to edit existing rate
     *
     * @var string
     */
    const URL_PATH_EDIT = 'mageworx_shippingrules/shippingrules_rate/edit';

    /**
     * Url path to remove existing rate
     *
     * @var string
     */
    const URL_PATH_DELETE = 'mageworx_shippingrules/shippingrules_rate/delete';

    /**
     * Rate id placeholder. Value for replace (url part) in the js
     */
    const RATE_ID_PLACEHOLDER = 'xentity_idx';

    /**
     * @var CarrierCollectionFactory
     */
    protected $carrierCollectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param MethodFactory $methodFactory
     * @param Registry $coreRegistry
     * @param StoreManagerInterface $storeManager
     * @param CarrierCollectionFactory $carrierCollectionFactory
     * @param RequestInterface $request
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        MethodFactory $methodFactory,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CarrierCollectionFactory $carrierCollectionFactory,
        RequestInterface $request
    ) {
        parent::__construct($arrayManager, $urlBuilder, $methodFactory, $coreRegistry, $storeManager);
        $this->carrierCollectionFactory = $carrierCollectionFactory;
        $this->request = $request;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var \MageWorx\ShippingRules\Model\Carrier\Method $method */
        $method = $this->getMethod();
        if ($method && $method->getId()) {
            $ratesCollection = $method->getRatesCollection();
            $rates = $ratesCollection->toArray();
            $ratesData = !empty($rates['items']) ? $rates['items'] : [];

            if (!empty($data[$method->getId()][static::DATA_SOURCE_DEFAULT][static::GRID_RATES_NAME])) {
                unset($data[$method->getId()][static::DATA_SOURCE_DEFAULT][static::GRID_RATES_NAME]);
            }

            return array_replace_recursive(
                $data,
                [
                    $method->getId() => [
                        static::DATA_SOURCE_DEFAULT => [
                            static::GRID_RATES_NAME => $ratesData,
                        ],
                    ],
                ]
            );
        }

        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        if ($this->getMethod() && $this->getMethod()->getId()) {
            $this->addRatesFieldset();
        }

        return $this->meta;
    }

    protected function addRatesFieldset()
    {
        $children = [];
        $children[static::CONTAINER_HEADER_NAME] = $this->getHeaderContainerConfig(10);
        if ($this->getMethod()->getRates()) {
            $children[static::GRID_RATES_NAME] = $this->getRatesGridConfig(20);
        }

        $this->meta[static::FIELD_RATES_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('%1', 'Rates Settings'),
                        'collapsible' => true,
                        'opened' => false,
                        'dataScope' => '',
                        'sortOrder' => 20,
                    ],
                ],
            ],
            'children' => $children
        ];
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder)
    {
        $children = [];
        if ($this->getMethod() && $this->getMethod()->getId()) {
            $children[static::BUTTON_ADD] = $this->getButtonSet();
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __('Rates for the Current Method'),
                    ],
                ],
            ],
            'children' => $children,
        ];
    }

    /**
     * Retrieve button set
     *
     * @return array
     */
    protected function getButtonSet()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/button',
                        'actions' => [
                            [
                                'targetName' =>
                                    'mageworx_shippingrules_method_form.mageworx_shippingrules_method_form',
                                'actionName' => 'addRate',
                                'params' => [
                                    true,
                                    [
                                        'redirectUrl' => $this->urlBuilder->getUrl(
                                            static::URL_PATH_NEW,
                                            [
                                                'method_id' => $this->getMethod()->getId(),
                                                RateController::BACK_TO_PARAM =>
                                                    RateController::BACK_TO_METHOD_PARAM,
                                            ]
                                        ),
                                    ],
                                ],
                            ],
                        ],
                        'title' => __('Add New Rate'),
                        'provider' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for the whole grid
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getRatesGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => null,
                        'collapsible' => false,
                        'dataScope' => 'data.method',
                        'visible' => true,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'mageworx_shippingrules_rates_listing',
                                'externalProvider' => 'mageworx_shippingrules_rates_listing.mageworx_shippingrules_rates_listing_data_source',
                                'selectionsProvider' => 'mageworx_shippingrules_rates_listing.mageworx_shippingrules_rates_listing.method_columns.ids',
                                'ns' => 'mageworx_shippingrules_rates_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => false,
                                'links' => [
                                    'insertData' => '${ $.provider }:${ $.dataProvider }'
                                ],
                                'imports' => [
                                    'methodId' => '${ $.provider }:data.entity_id'
                                ],
                                'exports' => [
                                    'methodId' => '${ $.externalProvider }:params.entity_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'rate_id' => $this->getTextColumn('rate_id', false, __('ID'), 0),
            'title' => $this->getTextColumn('title', false, __('Title'), 20),
            'active' => $this->getIsActiveColumn('active', true, __('Active'), 30),
            'price' => $this->getTextColumn('price', true, __('Price'), 40),
            'actionsList' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'text',
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'template' => 'MageWorx_ShippingRules/components/rate/actions-list',
                            'label' => __('Actions'),
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Number::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 80,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }

    /**
     * Retrieve is_active column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getIsActiveColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'MageWorx_ShippingRules/js/form/element/active',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                        'activeLabel' => __('Active'),
                        'inactiveLabel' => __('Inactive'),
                    ],
                ],
            ],
        ];

        return $column;
    }
}
