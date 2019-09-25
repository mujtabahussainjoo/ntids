<?php

namespace Infortis\UltraSlideshow\Model\Source\Navigation;

class Pagination
{
    public function toOptionArray()
    {
        return [
            ['value' => '',         'label' => __('Disabled')],
            ['value' => '1',        'label' => __('Style 1')],
            ['value' => '2',        'label' => __('Style 2')],
        ];
    }
}
