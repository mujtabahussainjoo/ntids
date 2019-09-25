<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Search;

class Mode
{
    public function toOptionArray()
    {
        return [
            ['value' => 's',        'label' => __('Standard')],
            ['value' => 'e',        'label' => __('Expanding')],
        ];
    }
}
