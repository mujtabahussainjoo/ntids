<?php

namespace Infortis\Base\Model\System\Config\Source\Css\Background;

class Positionx
{
    public function toOptionArray()
    {
		return [
			['value' => 'left',	'label' => __('left')],
            ['value' => 'center',	'label' => __('center')],
            ['value' => 'right',	'label' => __('right')]
        ];
    }
}