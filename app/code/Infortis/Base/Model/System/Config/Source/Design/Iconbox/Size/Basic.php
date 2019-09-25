<?php

namespace Infortis\Base\Model\System\Config\Source\Design\Iconbox\Size;

class Basic
{
    public function toOptionArray()
    {
        return [
            ['value' => 'l',    'label' => __('L')],
            ['value' => 'm',    'label' => __('M')],
            ['value' => 's',    'label' => __('S')],
            ['value' => 'xs',   'label' => __('XS')],
        ];
    }
}
