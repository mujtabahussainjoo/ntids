<?php

namespace Infortis\Base\Model\System\Config\Source\Category\Grid;

class Columncountmobile
{
    public function toOptionArray()
    {
        return [
			['value' => 1, 'label' => __('1')],
			['value' => 2, 'label' => __('2')],
			['value' => 3, 'label' => __('3')],
        ];
    }
}