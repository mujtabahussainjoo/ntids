<?php
/**
 * @deprecated
 */

namespace Infortis\Base\Model\System\Config\Source\Layout\Element;

class Replacewithblock
{
    public function toOptionArray()
    {
		return [
			['value' => 0, 'label' => __('Disable Completely')],
            ['value' => 1, 'label' => __('Don\'t Replace With Static Block')],
            ['value' => 2, 'label' => __('If Empty, Replace With Static Block')],
			['value' => 3, 'label' => __('Replace With Static Block')]
        ];
    }
}