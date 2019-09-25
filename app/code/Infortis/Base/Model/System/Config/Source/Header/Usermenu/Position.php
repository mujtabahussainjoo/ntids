<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Usermenu;

class Position
{
    public function toOptionArray()
    {
		return [
			['value' => '1',	'label' => __('Before Cart Drop-Down Block')],
			['value' => '2',	'label' => __('Before Compare Block')],
			['value' => '3',	'label' => __('Before Top Links')],
			['value' => '4',	'label' => __('After Top Links')],
        ];
    }
}