<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\Carrier\Form\Modifier;

use Magento\Framework\Phrase;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Method as MethodController;

class Methods extends AbstractModifier
{

    const FIELD_METHODS_NAME = 'method';
    const FIELD_IS_DELETE = 'is_delete';
    const FIELD_SORT_ORDER_NAME = 'sort_order';
    const FIELD_METHOD_ID = 'entity_id';
    const FIELD_CARRIER_ID = 'carrier_id';
    const FIELD_TITLE_NAME = 'title';
    const FIELD_CODE_NAME = 'code';
    const FIELD_PRICE_NAME = 'price';
    const FIELD_COST_NAME = 'cost';
    const FIELD_ACTIVE_NAME = 'active';


    /** Grid values */
    const GRID_METHODS_NAME = 'methods_listing';

    /** containers */
    const CONTAINER_HEADER_NAME = 'container_header';
    const CONTAINER_METHOD = 'container_method';
    const CONTAINER_COMMON_NAME = 'container_common';
    const CONTAINER_MODAL_NAME = 'method_modal';

    /** Buttons */
    const BUTTON_ADD = 'button_add';

    const CARRIER_METHODS_LISTING = 'methods_listing';

    /**
     * Url path to add a new method
     *
     * @var string
     */
    const URL_PATH_NEW = 'mageworx_shippingrules/shippingrules_method/new';

    /**
     * Url path to edit existing method
     *
     * @var string
     */
    const URL_PATH_EDIT = 'mageworx_shippingrules/shippingrules_method/edit';

    /**
     * Url path to remove existing method
     *
     * @var string
     */
    const URL_PATH_DELETE = 'mageworx_shippingrules/shippingrules_method/delete';

    /**
     * Method id placeholder. Value for replace (url part) in the js
     */
    const METHOD_ID_PLACEHOLDER = 'xentity_idx';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        // Add a methods data if the carrier exists
        /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
        $carrier = $this->getCarrier();
        if ($carrier && $carrier->getId()) {
            $methodsCollection = $carrier->getMethodsCollection();
            $methods = $methodsCollection->toArray();
            $methodsData = !empty($methods['items']) ? $methods['items'] : [];

            if (!empty($data[$carrier->getId()][static::DATA_SOURCE_DEFAULT][static::GRID_METHODS_NAME])) {
                unset($data[$carrier->getId()][static::DATA_SOURCE_DEFAULT][static::GRID_METHODS_NAME]);
            }

            return array_replace_recursive(
                $data,
                [
                    $carrier->getId() => [
                        static::DATA_SOURCE_DEFAULT => [
                            static::GRID_METHODS_NAME => $methodsData,
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
        if ($this->getCarrier() && $this->getCarrier()->getId()) {
            $this->addMethodsFieldset();
        }

        return $this->meta;
    }

    protected function addMethodsFieldset()
    {
        $children = [];
        $children[static::CONTAINER_HEADER_NAME] = $this->getHeaderContainerConfig(10);
        if ($this->getCarrier()->getMethods()) {
            $children[static::GRID_METHODS_NAME] = $this->getMethodsGridConfig(20);
        }

        $this->meta[static::FIELD_METHODS_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('%1', 'Methods Settings'),
                        'collapsible' => true,
                        'opened' => true,
                        'dataScope' => self::DATA_SCOPE_CARRIER,
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
        if ($this->getCarrier() && $this->getCarrier()->getId()) {
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
                        'content' => __('Shipping methods available on the checkout.'),
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
                                    'mageworx_shippingrules_carrier_form.mageworx_shippingrules_carrier_form',
                                'actionName' => 'addMethod',
                                'params' => [
                                    true,
                                    [
                                        'redirectUrl' => $this->urlBuilder->getUrl(
                                            static::URL_PATH_NEW,
                                            [
                                                'carrier_id' => $this->getCarrier()->getId(),
                                                MethodController::BACK_TO_PARAM =>
                                                    MethodController::BACK_TO_CARRIER_PARAM,
                                            ]
                                        ),
                                    ],
                                ],
                            ],
                        ],
                        'title' => __('Add Method'),
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
    protected function getMethodsGridConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Method'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'MageWorx_ShippingRules/js/dynamic-rows/method-dynamic-rows-grid',
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => static::FIELD_IS_DELETE,
                        'deleteValue' => '1',
                        'addButton' => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader' => true,
                        'collapsibleHeader' => true,
                        'sortOrder' => $sortOrder,
                        'provider' =>
                            'mageworx_shippingrules_carrier_form.mageworx_shippingrules_carrier_form_data_source',
                        'dataProvider' => 'methods_listing',
                        'source' => 'data.carrier',
                        'label' => null,
                        'columnsHeaderAfterRender' => true,
                        'recordTemplate' => 'record',
                        'deleteButtonLabel' => __('Remove'),
                        'editButtonLabel' => __('Edit'),
                        'identificationDRProperty' => 'entity_id',
                        'map' => [
                            'entity_id' => 'entity_id',
                            'carrier_id' => 'carrier_id',
                            'title' => 'title',
                            'code' => 'code',
                            'active' => 'active',
                            'price' => 'price',
                            'cost' => 'cost',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.parentScope }.carrier.methods_listing',
                        ],
                        'imports' => [
                            'currentCarrierId' => '${ $.provider }:${ $.parentScope }.carrier_id',
                        ],
                        'deleteRecordUrl' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'id' => static::METHOD_ID_PLACEHOLDER,
                                MethodController::BACK_TO_PARAM => MethodController::BACK_TO_CARRIER_PARAM,
                            ]
                        ),
                            'editRecordUrl' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                'id' => static::METHOD_ID_PLACEHOLDER,
                                MethodController::BACK_TO_PARAM => MethodController::BACK_TO_CARRIER_PARAM,
                                ]
                            ),
                            'idPlaceholder' => static::METHOD_ID_PLACEHOLDER,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::CONTAINER_METHOD . '.' . static::FIELD_SORT_ORDER_NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
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
            'entity_id' => $this->getTextColumn('entity_id', false, __('ID'), 0),
            'title' => $this->getTextColumn('title', false, __('Title'), 20),
            'active' => $this->getIsActiveColumn('active', true, __('Active'), 30),
            'code' => $this->getTextColumn('code', false, __('Method Code'), 40),
            'price' => $this->getTextColumn('price', true, __('Price'), 50),
            'cost' => $this->getTextColumn('cost', true, __('Cost'), 60),
            'actionsList' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'text',
                            'component' => 'Magento_Ui/js/form/element/abstract',
                            'template' => 'MageWorx_ShippingRules/components/actions-list',
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
