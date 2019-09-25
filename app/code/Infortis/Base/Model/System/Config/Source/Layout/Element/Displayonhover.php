<?php

namespace Infortis\Base\Model\System\Config\Source\Layout\Element;

class Displayonhover
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Don\'t Display')],
            ['value' => 1, 'label' => __('Display')],
            ['value' => 2, 'label' => __('Display On Hover')]
            // Previous values:
            // ['value' => 0, 'label' => __('Don\'t Display')],
            // ['value' => 1, 'label' => __('Display On Hover')],
            // ['value' => 2, 'label' => __('Display')]
        ];
    }
}
