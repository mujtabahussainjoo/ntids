<?php

namespace Infortis\Base\Model\System\Config\Source\Category\Grid\Hovereffect;

class Below
{
    public function toOptionArray()
    {
        return [
            ['value' => '',     'label' => __('-')],
            ['value' => '640',  'label' => __('640 px')],
            ['value' => '480',  'label' => __('480 px')],
            ['value' => '320',  'label' => __('320 px')],
        ];
    }
}
