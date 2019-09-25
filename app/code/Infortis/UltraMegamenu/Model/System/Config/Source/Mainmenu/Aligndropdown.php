<?php

namespace Infortis\UltraMegamenu\Model\System\Config\Source\Mainmenu;

class Aligndropdown
{
    public function toOptionArray()
    {
		return [
			['value' => 'window',				'label' => __('Viewport')],
			['value' => 'menuBar',				'label' => __('Menu bar')],
			['value' => 'headPrimInner',		'label' => __('Primary header, inner container')],
        ];
    }
}
