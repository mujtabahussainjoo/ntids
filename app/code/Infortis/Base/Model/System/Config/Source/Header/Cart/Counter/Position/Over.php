<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Cart\Counter\Position;

class Over
{
    public function toOptionArray()
    {
        return [
            ['value' => '',             'label' => __('-')],
            ['value' => 'top',          'label' => __('Over the icon, top right')],
            ['value' => 'bottom',       'label' => __('Over the icon, bottom right')],

            // ['value' => 'top-left',              'label' => __('Top left')],
            // ['value' => 'top-right',             'label' => __('Top right')],
            // ['value' => 'bottom-left',           'label' => __('Bottom left')],
            // ['value' => 'bottom-right',          'label' => __('Bottom right')],
        ];
    }
}
