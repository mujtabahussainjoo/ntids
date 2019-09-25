<?php

namespace Infortis\Base\Model\System\Config\Source\Layout\Screen\Width;

class Widecustom
{
    public function toOptionArray()
    {
		return [
            ['value' => '1920',     'label' => __('Breakpoint 3XL (1920 px)')],
            ['value' => '1680',     'label' => __('Breakpoint 2XL (1680 px)')],
            ['value' => '1440',     'label' => __('Breakpoint XL (1440 px)')],
            ['value' => '1200',     'label' => __('Breakpoint L (1200 px)')],
            ['value' => '992',      'label' => __('Breakpoint M (992 px)')],
            ['value' => '768',      'label' => __('Breakpoint S (768 px)')],
            ['value' => 'full',     'label' => __('Full width')],
            ['value' => 'custom',   'label' => __('Custom width...')],
        ];
    }
}
