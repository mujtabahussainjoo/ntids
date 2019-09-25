<?php

namespace Infortis\Base\Model\System\Config\Source\Css\Background;

class Repeat
{
    public function toOptionArray()
    {
		return [
			['value' => 'no-repeat',	'label' => __('no-repeat')],
            ['value' => 'repeat',		'label' => __('repeat')],
            ['value' => 'repeat-x',	'label' => __('repeat-x')],
			['value' => 'repeat-y',	'label' => __('repeat-y')]
        ];
    }
}