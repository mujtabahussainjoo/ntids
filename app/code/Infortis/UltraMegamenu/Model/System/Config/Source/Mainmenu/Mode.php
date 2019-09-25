<?php

namespace Infortis\UltraMegamenu\Model\System\Config\Source\Mainmenu;

class Mode
{
    public function toOptionArray()
    {
		return [
			['value' => '1',	'label' => __('Drop-down')],
			['value' => '0',	'label' => __('Drop-down/Mobile')],
			['value' => '-1',	'label' => __('Mobile')],

        ];
    }
}
