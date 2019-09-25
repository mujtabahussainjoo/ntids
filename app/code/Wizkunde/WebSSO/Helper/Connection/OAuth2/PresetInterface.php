<?php

namespace Wizkunde\WebSSO\Connection\OAuth2;

interface PresetInterface
{
    /**
     * @return string
     */
    public function getServerType();

    /**
     * @return string
     */
    public function getLoginUrl();

    /**
     * @return string
     */
    public function getScopePermissions();

    /**
     * @return string
     */
    public function getTokenEndpoint();

    /**
     * @return string
     */
    public function getUserinfoEndpoint();

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
