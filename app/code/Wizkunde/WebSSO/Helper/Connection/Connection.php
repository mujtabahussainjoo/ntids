<?php

namespace Wizkunde\WebSSO\Connection;

use \Magento\Framework\Exception\LocalizedException;

class Connection implements ConnectionInterface
{
    /**
     * Holds the connection instance
     * @var
     */
    private $connection;

    private $serverHelper;

    private $saml2Connection = null;
    private $oauth2Connection = null;
    private $backendHelper;
    private $frontendHelper;
    private $mappingHelper;
    private $customerTokenService;

    /**
     * @var \Wizkunde\WebSSO\Model\Server
     */
    private $serverData;

    public function __construct(
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Wizkunde\WebSSO\Model\Server $serverData,
        \Wizkunde\WebSSO\Connection\SAML2\Connection $saml2Connection,
        \Wizkunde\WebSSO\Connection\OAuth2\Connection $oauth2Connection,
        \Magento\Framework\App\State $mappingHelper,
        \Wizkunde\WebSSO\Helper\Backend $backendHelper,
        \Wizkunde\WebSSO\Helper\Frontend $frontendHelper,
        \Wizkunde\WebSSO\Api\CustomerTokenServiceInterface $customerTokenService
    ) {
        $this->serverHelper = $serverHelper;
        $this->serverData = $serverData;
        $this->saml2Connection = $saml2Connection;
        $this->oauth2Connection = $oauth2Connection;
        $this->mappingHelper = $mappingHelper;
        $this->backendHelper = $backendHelper;
        $this->frontendHelper = $frontendHelper;
        $this->customerTokenService = $customerTokenService;

        if ($this->serverHelper->getServerId() != false) {
            $this->serverData->load($this->serverHelper->getServerId());

            $connectionType =  $this->serverData->getConnectionType();

            if ($connectionType != '') {
                switch ($connectionType) {
                    case 'SAML2':
                        $this->connection = $this->saml2Connection;
                        break;
                    case 'OAuth2':
                        $this->connection = $this->oauth2Connection;
                        break;
                    default:
                        throw new LocalizedException('Cannot setup connection, unknown type set');
                }
            }
        }
    }

    public function authenticate()
    {
        return $this->connection->authenticate();
    }

    /**
     * @param mixed $request
     * @return string
     */
    public function getCustomerToken($request)
    {
        $this->connection->prepareLoginBySso($request);

        return $this->customerTokenService->createCustomerAccessToken($this->connection->getUserData());
    }

    /**
     * @param string $customerId
     * @return bool
     */
    public function revokeCustomerToken($customerId)
    {
        return $this->customerTokenService->revokeCustomerAccessToken($customerId);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function loginBySso(\Magento\Framework\App\RequestInterface $request)
    {
        $this->connection->prepareLoginBySso($request);

        if ($this->mappingHelper->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $this->backendHelper->loginAdminUser($this->connection);
        } else {
            $this->frontendHelper->loginUser($this->connection);
        }
    }

    /**
     * @return array
     */
    public function getLoginRequest()
    {
        return $this->connection->getLoginRequest();
    }

    public function logout()
    {
        return $this->connection->logout();
    }

    public function getUserData()
    {
        return $this->connection->getUserData();
    }

    public function persistSession()
    {
        return $this->connection->persistSession();
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return mixed
     */
    public function getServerData()
    {
        return $this->serverData;
    }

    /**
     * @param mixed $serverData
     */
    public function setServerData($serverData)
    {
        $this->serverData = $serverData;
    }
}
