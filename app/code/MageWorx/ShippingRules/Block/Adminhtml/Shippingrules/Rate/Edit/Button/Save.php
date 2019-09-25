<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Rate\Edit\Button;

use Magento\Ui\Component\Control\Container;
use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Rate as RateController;
use MageWorx\ShippingRules\Ui\DataProvider\Rate\Form\Modifier\AbstractModifier as Modifier;

class Save extends Generic
{
    /**
     * Get save button data with options: save & new; save & close;
     *
     * @return array
     */
    public function getButtonData()
    {
        $params = [
            false,
        ];
        if ($this->isBackToMethod() && $this->getRate()->getMethodId()) {
            $params = [
                true,
                [
                    RateController::BACK_TO_PARAM => RateController::BACK_TO_METHOD_PARAM,
                ],
            ];
        }

        $options = $this->getOptions();
        $data = [
            'label' => __('Save'),
            'class' => 'save primary',
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $options,
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => Modifier::FORM_NAME . '.' . Modifier::FORM_NAME,
                                'actionName' => 'save',
                                'params' => [
                                    $params,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
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
                                        'back' => 'newAction',
                                    ],
                                ],
                                'targetName' => Modifier::FORM_NAME . '.' . Modifier::FORM_NAME,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $options[] = [
            'label' => __('Save & Close'),
            'id_hard' => 'save_and_close',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                ],
                                'targetName' => Modifier::FORM_NAME . '.' . Modifier::FORM_NAME,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $options;
    }
}
