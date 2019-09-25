<?php

namespace Wizkunde\WebSSO\Connection\OAuth2;

use League\OAuth2\Client\Provider\GenericProvider;
use Wizkunde\WebSSO\Helper\Server;

class Provider extends GenericProvider
{
    protected $logoutUrl = '';

    /**
     * @param Server $serverHelper
     * @param array $collaborators
     */
    public function __construct(Server $serverHelper)
    {
        $serverData = $serverHelper->getServerInfo();

        $options = [];

        if (count($serverData) > 0 && $serverData['connection_type'] == 'OAuth2' && isset($serverData['type_oauth2'])) {
            $options['clientId']                = $serverData['type_oauth2']['client_id'];
            $options['clientSecret']            = $serverData['type_oauth2']['client_secret'];
            $options['urlAuthorize']            = $serverData['type_oauth2']['login_url'];
            $options['urlAccessToken']          = $serverData['type_oauth2']['token_endpoint'];
            $options['urlResourceOwnerDetails'] = $serverData['type_oauth2']['userinfo_endpoint'];
            $options['scopes']                  = [
                $serverData['type_oauth2']['scope_permissions']
            ];

            if(isset($serverData['type_oauth2']['logout_url']))
            {
                $this->logoutUrl = $serverData['type_oauth2']['logout_url'];
            }
        } else {
            $options['clientId']                = '';
            $options['clientSecret']            = '';
            $options['urlAuthorize']            = '';
            $options['urlAccessToken']          = '';
            $options['urlResourceOwnerDetails'] = '';
        }

        parent::__construct($options);
    }

    public function logout($accessToken)
    {
        $request = $this->getLogoutRequest($accessToken);

        $response = $this->getResponse($request);
    }

    public function getLogoutRequest($accessToken)
    {
        return $this->createRequest($this->getLogoutMethod(), $this->logoutUrl . '?token=' . urlencode($accessToken->getToken()), $accessToken, []);
    }

    /**
     * Returns the method to use when logging out
     *
     * @return string HTTP method
     */
    protected function getLogoutMethod()
    {
        return self::METHOD_GET;
    }
}
