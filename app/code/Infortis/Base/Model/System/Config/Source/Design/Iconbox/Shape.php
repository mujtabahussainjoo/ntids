<?php

namespace Infortis\Base\Model\System\Config\Source\Design\Iconbox;

class Shape
{
    public function toOptionArray()
    {
        return [
            ['value' => 'circle',       'label' => __('Circle (default)')],
            ['value' => 'rounded',      'label' => __('Rounded')],
            ['value' => 'square',       'label' => __('Square')],
        ];
    }
}
