<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\System;

class SurchargeBasis
{
    const BASED_ON_SUBTOTAL = 'subtotal';
    const BASED_ON_SHIPPING = 'shipping';

    public function toOptionArray()
    {
        return [
            ['value' => self::BASED_ON_SUBTOTAL, 'label' => __('Subtotal')],
            ['value' => self::BASED_ON_SHIPPING, 'label' => __('Shipping')]
        ];
    }
}
