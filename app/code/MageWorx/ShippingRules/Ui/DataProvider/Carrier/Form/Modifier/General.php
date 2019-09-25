<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Ui\DataProvider\Carrier\Form\Modifier;

use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class General
 */
class General extends AbstractModifier
{

    const FIELD_CARRIER_NAME = 'carrier';
    const FIELD_ENTITY_ID_NAME = 'carrier_id';
    const FIELD_TITLE_NAME = 'title';
    const FIELD_IS_ACTIVE_NAME = 'active';
    const FIELD_NAME_NAME = 'name';
    const FIELD_CARRIER_CODE_NAME = 'carrier_code';
    const FIELD_PRICE_NAME = 'price';
    const FIELD_UPDATE_RULES_NAME = 'update_rules';

    const KEY_SUBMIT_URL = 'submit_url';

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        // Add submit (save) url to the config
        $actionParameters = [];
        $submitUrl = $this->urlBuilder->getUrl('mageworx_shippingrules/shippingrules_carrier/save', $actionParameters);
        $data = array_replace_recursive(
            $data,
            [
                'config' => [
                    self::KEY_SUBMIT_URL => $submitUrl,
                ]
            ]
        );

        // Add a carrier data if the carrier exists
        /** @var \MageWorx\ShippingRules\Model\Carrier $carrier */
        $carrier = $this->getCarrier();
        $carrierData = $carrier->getData();
        $carrierData['single_store'] = $this->storeManager->isSingleStoreMode();

        if ($carrier && $carrier->getId()) {
            return array_replace_recursive(
                $data,
                [
                    $carrier->getId() => [
                        static::DATA_SOURCE_DEFAULT => $carrierData,
                    ],
                ]
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->buildMainFields();

        return $this->meta;
    }

    /**
     * Adds main fields to the metadata
     *
     * @return array
     */
    protected function buildMainFields()
    {
        $this->meta[static::FIELD_CARRIER_NAME] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('%1', 'Carrier Settings'),
                        'collapsible' => true,
                        'opened' => true,
                        'dataScope' => self::DATA_SCOPE_CARRIER,
                        'sortOrder' => 10,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_ENTITY_ID_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Id'),
                                'componentType' => Field::NAME,
                                'formElement' => Hidden::NAME,
                                'dataScope' => static::FIELD_ENTITY_ID_NAME,
                                'dataType' => Number::NAME,
                                'sortOrder' => 0,
                            ],
                        ],
                    ],
                ],
                static::FIELD_TITLE_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Default Title'),
                                'componentType' => Field::NAME,
                                'formElement' => Input::NAME,
                                'dataScope' => static::FIELD_TITLE_NAME,
                                'dataType' => Text::NAME,
                                'sortOrder' => 10,
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                static::FIELD_IS_ACTIVE_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Is Active'),
                                'componentType' => Field::NAME,
                                'formElement' => Checkbox::NAME,
                                'dataScope' => static::FIELD_IS_ACTIVE_NAME,
                                'dataType' => Number::NAME,
                                'sortOrder' => 20,
                                'prefer' => 'toggle',
                                'valueMap' => [
                                    'true' => '1',
                                    'false' => '0',
                                ],
                            ],
                        ],
                    ],
                ],
                static::FIELD_NAME_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('System Name'),
                                'componentType' => Field::NAME,
                                'formElement' => Input::NAME,
                                'dataScope' => static::FIELD_NAME_NAME,
                                'dataType' => Text::NAME,
                                'validation' => [
                                    'required-entry' => true,
                                ],
                                'sortOrder' => 30,
                            ],
                        ],
                    ],
                ],
                static::FIELD_CARRIER_CODE_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Carrier Code'),
                                'componentType' => Field::NAME,
                                'formElement' => Input::NAME,
                                'dataScope' => static::FIELD_CARRIER_CODE_NAME,
                                'dataType' => Text::NAME,
                                'validation' => [
                                    'required-entry' => true,
                                    'validate-code' => true,
                                    'min_text_len‌​gth' => 1,
                                    'max_text_length' => 19
                                ],
                                'sortOrder' => 40,
                            ],
                        ],
                    ],
                ],
                static::FIELD_PRICE_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Price'),
                                'componentType' => Field::NAME,
                                'formElement' => Hidden::NAME,
                                'dataScope' => static::FIELD_PRICE_NAME,
                                'dataType' => Number::NAME,
                                'addbefore' => $this->getBaseCurrencySymbol(),
                                'sortOrder' => 50,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $this->meta;
    }
}
