<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Cart\Iconbox;

class Size
{
    public function toOptionArray()
    {
        return [
            ['value' => '0',    'label' => __('No iconbox')],
            ['value' => 'l',    'label' => __('L')],
            ['value' => 'm',    'label' => __('M')],
            ['value' => 's',    'label' => __('S')],
        ];
    }
}
