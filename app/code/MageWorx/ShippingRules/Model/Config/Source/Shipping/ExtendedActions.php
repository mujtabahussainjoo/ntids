<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Config\Source\Shipping;

use Magento\Framework\Option\ArrayInterface;
use MageWorx\ShippingRules\Model\Rule;

class ExtendedActions implements ArrayInterface
{

    /**
     * Return array of available actions.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result['shipping_cost'] = [
            'label' => __('Shipping Cost'),
            'value' => [
                [
                    'label' => __('Overwrite Amount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ],
                [
                    'label' => __('Overwrite Amount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ]
            ]
        ];

        $result['shipping_cost_per_qty_of_item'] = [
            'label' => __('Shipping Cost per Product'),
            'value' => [
                [
                    'label' => __('Overwrite Amount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ],
                [
                    'label' => __('Overwrite Amount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_cost_per_item'] = [
            'label' => __('Shipping Cost Per Item'),
            'value' => [
                [
                    'label' => __('Overwrite Amount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ],
                [
                    'label' => __('Overwrite Amount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_cost_per_weight'] = [
            'label' => __('Shipping Cost Per 1 Unit of Weight'),
            'value' => [
                [
                    'label' => __('Overwrite Amount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ],
                [
                    'label' => __('Overwrite Amount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_OVERWRITE,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ]
            ]
        ];

        $result['shipping_surcharge'] = [
            'label' => __('Shipping Surcharge'),
            'value' => [
                [
                    'label' => __('Add Surcharge (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ],
                [
                    'label' => __('Add Surcharge (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ]
            ]
        ];

        $result['shipping_surcharge_per_qty_of_item'] = [
            'label' => __('Shipping Surcharge per Product'),
            'value' => [
                [
                    'label' => __('Add Surcharge (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ],
                [
                    'label' => __('Add Surcharge (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_surcharge_per_item'] = [
            'label' => __('Shipping Surcharge per Item'),
            'value' => [
                [
                    'label' => __('Add Surcharge (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ],
                [
                    'label' => __('Add Surcharge (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_surcharge_per_weight'] = [
            'label' => __('Shipping Surcharge per 1 Unit of Weight'),
            'value' => [
                [
                    'label' => __('Add Surcharge (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ],
                [
                    'label' => __('Add Surcharge (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_SURCHARGE,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ]
            ]
        ];

        $result['shipping_discount'] = [
            'label' => __('Shipping Discount'),
            'value' => [
                [
                    'label' => __('Add Discount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ],
                [
                    'label' => __('Add Discount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_AMOUNT
                    ])
                ]
            ]
        ];

        $result['shipping_discount_per_qty_of_item'] = [
            'label' => __('Shipping Discount per Product'),
            'value' => [
                [
                    'label' => __('Add Discount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ],
                [
                    'label' => __('Add Discount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_QTY_OF_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_discount_per_item'] = [
            'label' => __('Shipping Discount per Item'),
            'value' => [
                [
                    'label' => __('Add Discount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ],
                [
                    'label' => __('Add Discount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_ITEM
                    ])
                ]
            ]
        ];

        $result['shipping_discount_per_weight'] = [
            'label' => __('Shipping Discount per 1 Unit of Weight'),
            'value' => [
                [
                    'label' => __('Add Discount (Fixed)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_FIXED,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ],
                [
                    'label' => __('Add Discount (Percent)'),
                    'value' => implode('_', [
                        Rule::ACTION_CALCULATION_PERCENT,
                        Rule::ACTION_METHOD_DISCOUNT,
                        Rule::ACTION_TYPE_PER_WEIGHT_UNIT
                    ])
                ]
            ]
        ];

        return $result;
    }
}
