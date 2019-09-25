<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Position;

class Primarymenucontainer
{
    public function toOptionArray()
    {
        return [
            ['value' => 'menuContainer',        'label' => __('Full Width Menu Container')],
            ['value' => 'topCentral',           'label' => __('Top, Central')],
            ['value' => 'primLeftCol',          'label' => __('Primary, Left Column')],
            ['value' => 'primCentralCol',       'label' => __('Primary, Central Column')],
            ['value' => 'primRightCol',         'label' => __('Primary, Right Column')],
        ];
    }
}
