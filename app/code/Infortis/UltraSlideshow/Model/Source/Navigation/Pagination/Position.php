<?php

namespace Infortis\UltraSlideshow\Model\Source\Navigation\Pagination;

class Position
{
    public function toOptionArray()
    {
        return [
            ['value' => 'bottom-centered',          'label' => __('Bottom, centered')],
            ['value' => 'bottom-left',              'label' => __('Bottom, left')],
            ['value' => 'bottom-right',             'label' => __('Bottom, right')],
            ['value' => 'over-bottom-centered',     'label' => __('Bottom, centered, over the slides')],
            ['value' => 'over-bottom-left',         'label' => __('Bottom, left, over the slides')],
            ['value' => 'over-bottom-right',        'label' => __('Bottom, right, over the slides')],
        ];
    }
}
