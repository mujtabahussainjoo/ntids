<?php

namespace Wizkunde\WebSSO\Model\Config\Source;

class Severity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'all', 'label' => 'All Login Attempts'], ['value' => 'failed', 'label' => 'Failed Login Attempts']];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['all' => 'All Login Attempts', 'failed' => 'Failed Login Attempts'];
    }
}
