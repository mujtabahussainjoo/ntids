<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Carrier\Edit\Button;

use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 */
class Save extends Generic
{
    const TARGET_FORM_NAME = 'mageworx_shippingrules_carrier_form.mageworx_shippingrules_carrier_form';

    /**
     * Get save button data with options: save & new; save & close;
     *
     * @return array
     */
    public function getButtonData()
    {
        $options = $this->getOptions();
        $data = [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => self::TARGET_FORM_NAME,
                                'actionName' => 'save',
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'options' => $options,
            'class_name' => Container::SPLIT_BUTTON,
        ];

        return $data;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'label' => __('Save & New'),
            'id_hard' => 'save_and_new',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'newAction'
                                    ]
                                ],
                                'targetName' => self::TARGET_FORM_NAME,
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }
}
