<?php

namespace Infortis\Base\Model\System\Config\Source\Category;

class Altimagecolumn
{
    public function toOptionArray()
    {
        return [
            ['value' => 'label',            'label' => __('Label (Alt Text)')],
            ['value' => 'position',         'label' => __('Sort Order')],
        ];
    }
}
