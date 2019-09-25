<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Usermenu\LineBreak;
class Position
{
    public function toOptionArray()
    {
		return [
			['value' => '0',	'label' => __('No Additional Line Break')],
			['value' => '30',	'label' => __('Before Cart Drop-Down Block')],
			['value' => '31',	'label' => __('After Cart Drop-Down Block')],
			// ['value' => '32',	'label' => __('Before Compare Block')],
			// ['value' => '33',	'label' => __('After Compare Block')],
			['value' => '34',	'label' => __('Before Top Links')],
			['value' => '35',	'label' => __('After Top Links')],
        ];
    }
}