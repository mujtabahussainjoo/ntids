<?php

namespace Infortis\Base\Model\System\Config\Source\Design\Section;

class Sidepaddingvalue
{
	public function toOptionArray()
	{
		return [
			//If no value selected, use default side padding of the page
			['value' => '',				'label' => __('Use Default')],
			//No side padding
			['value' => 'expanded',		'label' => __('No Side Padding')],
			//Full-width inner container
			['value' => 'full',			'label' => __('Full Width')],
			//Full-width inner container, no side padding
			['value' => 'full-expanded','label' => __('Full Width, No Side Padding')],
			//Override the default value
			['value' => 'override',		'label' => __('Override Default Value...')],
		];
	}
}