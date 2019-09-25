<?php

namespace Infortis\Base\Model\System\Config\Source\Import;

class Demo
{
    public function toOptionArray()
    {
        $numberOfDemos = 8;
        $array = [];

        for ($i = 1; $i < ($numberOfDemos + 1); $i++)
        {
            $array[] = ['value' => $i, 'label' => 'Demo ' . $i];
        }

        return $array;
    }
}
