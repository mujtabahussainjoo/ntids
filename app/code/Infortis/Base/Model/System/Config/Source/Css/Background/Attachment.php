<?php

namespace Infortis\Base\Model\System\Config\Source\Css\Background;

class Attachment
{
    public function toOptionArray()
    {
		return [
			['value' => 'fixed',	'label' => __('fixed')],
            ['value' => 'scroll',	'label' => __('scroll')]
        ];
    }
}