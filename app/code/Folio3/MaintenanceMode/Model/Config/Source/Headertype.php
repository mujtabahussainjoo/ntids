<?php

namespace Folio3\MaintenanceMode\Model\Config\Source;

class Headertype implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            0 => 'Disabled',
            1 => 'Store Name',
            2 => 'Store Logo'
        );
    }
}