<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\ExtendedZone\Edit\Button;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone as ExtendedZoneController;

class SaveAndContinue extends Generic
{
    /**
     * Get save button data with options: save & new; save & close;
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
        ];

        return $data;
    }
}
