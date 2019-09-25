<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Serole\Serialcode\Ui\Component\Listing\Codes\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;


    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
		$option[0]['label'] = "Assigned";
		$option[0]['value'] = 1;
		$option[1]['label'] = "Released";
		$option[1]['value'] = 0;
		$option[2]['label'] = "Invalid";
		$option[2]['value'] = 2;
        
		return $option;
    }
}
