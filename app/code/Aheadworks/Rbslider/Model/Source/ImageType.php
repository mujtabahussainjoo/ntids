<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Source;

/**
 * Class ImageType
 * @package Aheadworks\Rbslider\Model\Source
 */
class ImageType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Image type values
     */
    const TYPE_FILE = 1;
    const TYPE_URL = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_FILE,  'label' => __('File')],
            ['value' => self::TYPE_URL,  'label' => __('URL')],
        ];
    }
}
