<?php

namespace Infortis\UltraSlideshow\Model\Source\Banners;

class Position
{
    public function toOptionArray()
    {
        return [
			['value' => 'left',	'label' => __('Left')],
			['value' => 'right',	'label' => __('Right')]
        ];
    }
}
