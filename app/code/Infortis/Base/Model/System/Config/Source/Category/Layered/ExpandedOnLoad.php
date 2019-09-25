<?php

namespace Infortis\Base\Model\System\Config\Source\Category\Layered;
class ExpandedOnLoad
{
    public function toOptionArray()
    {
        return [
            ['value' => 'false',                                'label' => __('None')],
            ['value' => '[0]',                                  'label' => __('First filter')],
            ['value' => '[0,1]',                                'label' => __('First two filters')],
            ['value' => '[0,1,2]',                              'label' => __('First three filters')],
            ['value' => '[0,1,2,3]',                            'label' => __('First four filters')],
            ['value' => '[0,1,2,3,4,5,6,7,8,9,10,11,12]',       'label' => __('All')],
        ];
    }
}
