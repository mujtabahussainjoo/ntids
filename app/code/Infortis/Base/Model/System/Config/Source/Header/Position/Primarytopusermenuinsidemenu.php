<?php

namespace Infortis\Base\Model\System\Config\Source\Header\Position;

class Primarytopusermenuinsidemenu
{
    public function toOptionArray()
    {
        return [
            ['value' => 'topLeft',              'label' => __('Top, Left')],
            ['value' => 'topCentral',           'label' => __('Top, Central')],
            ['value' => 'topRight',             'label' => __('Top, Right')],
            ['value' => 'primLeftCol',          'label' => __('Primary, Left Column')],
            ['value' => 'primCentralCol',       'label' => __('Primary, Central Column')],
            ['value' => 'primRightCol',         'label' => __('Primary, Right Column')],
            ['value' => 'mainMenu',             'label' => __('Inside Main Menu')],
            ['value' => 'userMenu',             'label' => __('Inside User Menu...')],
        ];
    }
}
