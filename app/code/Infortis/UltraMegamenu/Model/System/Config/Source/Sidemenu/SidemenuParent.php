<?php

namespace Infortis\UltraMegamenu\Model\System\Config\Source\Sidemenu;
class SidemenuParent
{
    public function toOptionArray()
    {
        return [
            ['value' => 'root',                'label' => __('Root - show top-level categories')],
            ['value' => 'parent',              'label' => __('Parent of current category - show current category and its siblings')],
            ['value' => 'parent_no_siblings',  'label' => __('Parent of current category (no siblings) - show current category')],
            ['value' => 'current',             'label' => __('Current category - show subcategories of current category')],
        ];
    }
}
