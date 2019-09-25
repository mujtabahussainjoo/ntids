<?php

namespace Wizkunde\WebSSO\Connection\SAML2;

interface PresetInterface
{
    /**
     * @return \Magento\Framework\Option\ArrayInterface
     */
    public function getMappings();

    /**
     * @param \Magento\Framework\Option\ArrayInterface $mappings
     * @return void
     */
    public function setMappings(\Magento\Framework\Option\ArrayInterface $mappings);
}
