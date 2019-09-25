<?php

namespace Wizkunde\WebSSO\Connection\OAuth2\Preset;

use Wizkunde\WebSSO\Connection\OAuth2\PresetInterface;

class Google implements PresetInterface
{
    private $mappings;

    public function getServerType()
    {
        return 'oauth2';
    }

    public function getLoginUrl()
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth';
    }

    public function getScopePermissions()
    {
        return 'email profile';
    }

    public function getTokenEndpoint()
    {
        return 'https://www.googleapis.com/oauth2/v4/token';
    }

    public function getUserinfoEndpoint()
    {
        return 'https://www.googleapis.com/plus/v1/people/me';
    }

    public function getMappings()
    {
        return $this->mappings;
    }
    
    public function setMappings(\Magento\Framework\Option\ArrayInterface $mappings)
    {
        $this->mappings = $mappings;
    }
}
