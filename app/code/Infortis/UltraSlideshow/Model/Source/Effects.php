<?php

namespace Infortis\UltraSlideshow\Model\Source;

class Effects
{
	public function toOptionArray()
	{
		return [
			['value' => '',				'label' => __(' ')],
			['value' => 'fade',			'label' => __('fade')],
			['value' => 'backSlide',		'label' => __('backSlide')],
			['value' => 'goDown',			'label' => __('goDown')],
			['value' => 'fadeUp',			'label' => __('fadeUp')],
		];
	}
}
