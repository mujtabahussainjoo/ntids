<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Rate\Edit\Button;

use Magento\Ui\Component\Control\Container;

class Duplicate extends Generic
{
    /**
     * Get Duplicate button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getRate() && $this->getRate()->getId()) {
            $methodId = $this->getRate()->getId();
            $url = $this->getUrl(
                'mageworx_shippingrules/shippingrules_rate/duplicate',
                ['id' => $methodId]
            );
            $onClick = sprintf("location.href = '%s';", $url);
            $data = [
                'label' => __('Duplicate'),
                'class' => 'duplicate',
                'class_name' => Container::DEFAULT_CONTROL,
                'options' => [],
                'on_click' => $onClick,
            ];
        }

        return $data;
    }
}
