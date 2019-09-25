<?php

namespace Infortis\Base\Model\System\Config\Source\Product\Position;

class All
{
    public function toOptionArray()
    {
        return [
            ['value' => 'primCol_1',            'label' => __('Primary Column, Position 1')],
            ['value' => 'primCol_2',            'label' => __('Primary Column, Position 2')],
            //array('value' => 'primCol_3',     'label' => __('Primary Column, Position 3')),

            ['value' => 'secCol_1',             'label' => __('Secondary Column, Position 1')],
            ['value' => 'secCol_2',             'label' => __('Secondary Column, Position 2')],
            //['value' => 'secCol_3',           'label' => __('Secondary Column, Position 3')],

            ['value' => 'lowerPrimCol_1',       'label' => __('Lower Primary Column')], //'Lower Primary Column, Position 1'
            //['value' => 'lowerPrimCol_2',     'label' => __('Lower Primary Column, Position 2')],

            ['value' => 'lowerSecCol_1',        'label' => __('Lower Secondary Column')], //'Lower Secondary Column, Position 1'
            //['value' => 'lowerSecCol_2',      'label' => __('Lower Secondary Column, Position 2')],
        ];
    }
}
