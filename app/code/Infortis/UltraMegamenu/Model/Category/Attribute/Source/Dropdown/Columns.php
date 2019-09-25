<?php

namespace Infortis\UltraMegamenu\Model\Category\Attribute\Source\Dropdown;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Columns
    extends AbstractSource
{
    protected $_options;
    
    /**
     * Get list of available number of columns
     */
    public function getAllOptions()
    {
        if (!$this->_options)
        {
            $this->_options = [
                ['value' => '',     'label' => ' '],
                ['value' => 1,      'label' => '1'],
                ['value' => 2,      'label' => '2'],
                ['value' => 3,      'label' => '3'],
                ['value' => 4,      'label' => '4'],
                ['value' => 5,      'label' => '5'],
                ['value' => 6,      'label' => '6'],
                ['value' => 7,      'label' => '7'],
                ['value' => 8,      'label' => '8'],
            ];
        }
        return $this->_options;
    }
}
