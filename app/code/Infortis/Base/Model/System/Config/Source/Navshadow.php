<?php

namespace Infortis\Base\Model\System\Config\Source;

class Navshadow
{
    public function toOptionArray()
    {
        return [
            ['value' => '',                     'label' => __('None')],
			['value' => 'inner-container',      'label' => __('Inner container')],
			['value' => 'bar',                  'label' => __('Menu items')],
        ];
    }
}