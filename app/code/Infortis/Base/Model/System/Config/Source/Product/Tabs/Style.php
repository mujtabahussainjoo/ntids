<?php

namespace Infortis\Base\Model\System\Config\Source\Product\Tabs;

class Style
{
    public function toOptionArray()
    {
        return [
            ['value' => 'style1',               'label' => __('Style 1')],
            ['value' => 'style1 style1-small',  'label' => __('Style 1 - small')],
            ['value' => '',                     'label' => __('Blank theme tabs')],
            ['value' => 'style-luma',           'label' => __('Luma theme tabs')],
        ];
    }
}
