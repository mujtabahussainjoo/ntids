<?php

namespace Infortis\UltraMegamenu\Model\System\Config\Source;

class Below
{
    public function toOptionArray()
    {
        return [
            ['value' => '',     'label' => __('-')],
            ['value' => '640',  'label' => __('640 px')],
            ['value' => '480',  'label' => __('480 px')],
            ['value' => '320',  'label' => __('320 px')],
            ['value' => '240',  'label' => __('240 px')],
        ];
    }
}
