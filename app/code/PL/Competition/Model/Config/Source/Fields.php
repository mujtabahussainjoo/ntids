<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 6/1/2016
 * Time: 9:37 AM
 */
namespace PL\Competition\Model\Config\Source;

class Fields{

    public function toOptionArray()
    {
        return [
            ['value' => 'is_disclaimer', 'label' => __('Disclaimer')],
            ['value' => 'is_dob', 'label' => __('Date Of Birth')]
        ];
    }

}