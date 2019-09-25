<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Position;

class Primary
{
    public function toOptionArray()
    {
        return [
            ['value' => 'primLeftCol',          'label' => __('Primary, Left Column')],
            ['value' => 'primCentralCol',       'label' => __('Primary, Central Column')],
            ['value' => 'primRightCol',         'label' => __('Primary, Right Column')],
        ];
    }
}