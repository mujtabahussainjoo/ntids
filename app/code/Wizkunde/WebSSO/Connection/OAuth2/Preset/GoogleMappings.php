<?php
/**
 * Used in creating options for Server Type config value selection
 *
 */
namespace Wizkunde\WebSSO\Connection\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'emails.0.value',
                'label' => __('username')
            ],
            [
                'value' => 'name.familyName',
                'label' => __('lastname')
            ],
            [
                'value' => 'name.givenName',
                'label' => __('firstname')
            ]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'emails.0.value' => 'username',
            'name.familyName' => 'lastname',
            'name.givenName' => 'firstname'
        ];
    }
}
