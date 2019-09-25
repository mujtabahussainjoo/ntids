<?php

namespace Infortis\Base\Model\System\Config\Source\Product\Tabs;

class Mode
{
    public function toOptionArray()
    {
    	//Important: note the order of values - zero moved to the end
		return [
			['value' => 1,		'label' => __('Tabs')],
			['value' => 2,		'label' => __('Accordion')],
			['value' => 0,		'label' => __('Vertically stacked blocks')],
        ];
    }
}