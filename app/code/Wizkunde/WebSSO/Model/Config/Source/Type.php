<?php
/**
 * Used in creating options for Server Type config value selection
 *
 */
namespace Wizkunde\WebSSO\Model\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'saml2', 'label' => 'SAML2'], ['value' => 'oauth2', 'label' => 'OAuth2']];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['saml2' => 'SAML2', 'oauth2' => 'OAuth2'];
    }
}
