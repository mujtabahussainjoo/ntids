<?php
/**
 * @deprecated
 */

namespace Infortis\UltraSlideshow\Model\Source;

class Easing
{
    public function toOptionArray()
    {
        return [
			//Ease in-out
			['value' => 'easeInOutSine',	'label' => __('easeInOutSine')],
			['value' => 'easeInOutQuad',	'label' => __('easeInOutQuad')],
			['value' => 'easeInOutCubic',	'label' => __('easeInOutCubic')],
			['value' => 'easeInOutQuart',	'label' => __('easeInOutQuart')],
			['value' => 'easeInOutQuint',	'label' => __('easeInOutQuint')],
			['value' => 'easeInOutExpo',	'label' => __('easeInOutExpo')],
			['value' => 'easeInOutCirc',	'label' => __('easeInOutCirc')],
			['value' => 'easeInOutElastic','label' => __('easeInOutElastic')],
			['value' => 'easeInOutBack',	'label' => __('easeInOutBack')],
			['value' => 'easeInOutBounce',	'label' => __('easeInOutBounce')],
			//Ease out
			['value' => 'easeOutSine',		'label' => __('easeOutSine')],
			['value' => 'easeOutQuad',		'label' => __('easeOutQuad')],
			['value' => 'easeOutCubic',	'label' => __('easeOutCubic')],
			['value' => 'easeOutQuart',	'label' => __('easeOutQuart')],
			['value' => 'easeOutQuint',	'label' => __('easeOutQuint')],
			['value' => 'easeOutExpo',		'label' => __('easeOutExpo')],
			['value' => 'easeOutCirc',		'label' => __('easeOutCirc')],
			['value' => 'easeOutElastic',	'label' => __('easeOutElastic')],
			['value' => 'easeOutBack',		'label' => __('easeOutBack')],
			['value' => 'easeOutBounce',	'label' => __('easeOutBounce')],
			//Ease in
			['value' => 'easeInSine',		'label' => __('easeInSine')],
			['value' => 'easeInQuad',		'label' => __('easeInQuad')],
			['value' => 'easeInCubic',		'label' => __('easeInCubic')],
			['value' => 'easeInQuart',		'label' => __('easeInQuart')],
			['value' => 'easeInQuint',		'label' => __('easeInQuint')],
			['value' => 'easeInExpo',		'label' => __('easeInExpo')],
			['value' => 'easeInCirc',		'label' => __('easeInCirc')],
			['value' => 'easeInElastic',	'label' => __('easeInElastic')],
			['value' => 'easeInBack',		'label' => __('easeInBack')],
			['value' => 'easeInBounce',	'label' => __('easeInBounce')],
			//No easing
			['value' => 'none',			'label' => __('Disabled')]
        ];
    }
}
