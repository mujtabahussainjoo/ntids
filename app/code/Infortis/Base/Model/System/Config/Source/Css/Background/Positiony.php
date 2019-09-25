<?php

namespace Infortis\Base\Model\System\Config\Source\Css\Background;

class Positiony
{
    public function toOptionArray()
    {
		return [
			['value' => 'top',		'label' => __('top')],
            ['value' => 'center',	'label' => __('center')],
            ['value' => 'bottom',	'label' => __('bottom')]
        ];
    }
}