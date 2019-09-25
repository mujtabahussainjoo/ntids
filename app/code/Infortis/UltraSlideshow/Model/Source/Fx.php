<?php
/**
 * @deprecated
 */

namespace Infortis\UltraSlideshow\Model\Source;

class Fx
{
    public function toOptionArray()
    {
        return [
			['value' => 'slide',	'label' => __('Slide')],
			['value' => 'fade',	'label' => __('Fade')]
        ];
    }
}
