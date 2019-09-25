<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Source;

/**
 * Class Position
 * @package Aheadworks\Rbslider\Model\Source
 */
class Position implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Position values
     */
    const MENU_TOP = 1;
    const MENU_BOTTOM = 2;
    const CONTENT_TOP = 3;
    const PAGE_BOTTOM = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::MENU_TOP,  'label' => __('Menu Top')],
            ['value' => self::MENU_BOTTOM,  'label' => __('Menu Bottom')],
            ['value' => self::CONTENT_TOP,  'label' => __('Content top')],
            ['value' => self::PAGE_BOTTOM,  'label' => __('Page bottom')]
        ];
    }
}
