<?php

namespace Infortis\Base\Model\System\Config\Source\Product\AssociatedProducts;

class Template
{
    public function toOptionArray()
    {
        return [

            // Magento's default template file:
            ['value' => 'product/list/items.phtml',
                'label' => __('Simple Grid')],

            // Custom template files:
            ['value' => 'Infortis_Base::product/list/slider.phtml',
                'label' => __('Slider')],

        ];
    }
}
