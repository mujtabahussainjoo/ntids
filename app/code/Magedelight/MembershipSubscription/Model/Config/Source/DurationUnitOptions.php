<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class DurationUnitOptions implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Day',
                'value' => 'Day'
            ],
            1 => [
                'label' => 'Week',
                'value' => "Week"
            ],
            2  => [
                'label' => 'Month',
                'value' => "Month"
            ],
            3 => [
                'label' => 'Year',
                'value' => "Year"
            ],
        ];
        
        return $options;
    }
}
