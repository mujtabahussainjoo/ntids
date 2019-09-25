<?php

namespace Wizkunde\WebSSO\Connection\SAML2;

use Wizkunde\WebSSO\Connection\ConnectionInterface;

class Connection implements ConnectionInterface
{
    private $serverHelper = null;
    private $userData = [];
    private $attributeMapper = null;
    private $binding = null;
    private $signature = null;
    private $response = null;
    private $serverInfo = null;
    private $serverData;
    private $mappingHelper;
    private $backendHelper;
    private $frontendHelper;

    public function __construct(
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Wizkunde\WebSSO\Model\Server $serverData,
        \Wizkunde\WebSSO\Connection\SAML2\Service\Binding $binding,
        \Wizkunde\WebSSO\Connection\SAML2\Service\Signature $signature,
        \Wizkunde\WebSSO\Connection\SAML2\Service\Response $response,
        \Wizkunde\SAMLBase\Claim\Attributes $attributeMapper,
        \Magento\Framework\App\State $mappingHelper,
        \Wizkunde\WebSSO\Helper\Backend $backendHelper,
        \Wizkunde\WebSSO\Helper\Frontend $frontendHelper
    ) {
        $this->serverHelper = $serverHelper;
        $this->serverData = $serverData;
        $this->binding = $binding;
        $this->signature = $signature;
        $this->attributeMapper = $attributeMapper;
        $this->response = $response;

        $this->mappingHelper = $mappingHelper;
        $this->frontendHelper = $frontendHelper;
        $this->backendHelper = $backendHelper;

        $this->serverInfo = [];

        if ($this->serverHelper->getServerId() != false) {
            $this->serverData->load($this->serverHelper->getServerId());
            $this->serverInfo = $this->serverData->getData();
        }
    }

    public function getMetadataXml($backend = false)
    {
        return $this->binding->getMetadataXml($backend);
    }

    public function getServerInfo()
    {
        return $this->serverInfo;
    }
    
    /**
     * Authenticate against the connection source
     *
     * @return void
     */
    public function authenticate()
    {
        if ($this->serverHelper->getServerId() != false) {
            $this->binding->getSsoBinding()->request('AuthnRequest');
        }
    }

    /**
     * Get the SAML2 Login Request
     * @return array
     */
    public function getLoginRequest()
    {
        if ($this->serverHelper->getServerId() != false) {
            $this->binding->getSsoBinding()->getRequestData('AuthnRequest');
        }
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|string
     */
    public function loginBySso(\Magento\Framework\App\RequestInterface $request)
    {
        $this->prepareLoginBySso($request);

        if ($this->mappingHelper->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            return $this->backendHelper->loginAdminUser($this);
        } else {
            return $this->frontendHelper->loginUser($this);
        }
    }

    public function prepareLoginBySso(\Magento\Framework\App\RequestInterface $request)
    {
        $responseData = $this->handleSAMLResponse($request);
        $attributes = $this->getAttributes($responseData);
        $claimData = $this->serverHelper->getMappings($attributes);

        $this->setUserData($claimData);
    }

    /**
     * Get the SAML2 Logout Request
     */
    public function getLogoutRequest()
    {
        if ($this->serverHelper->getServerId() != false) {
            $this->binding->getSloBinding()->getRequestData('LogoutRequest');
        }
    }

    /**
     * Logout from the connection source
     * @return $this
     */
    public function logout()
    {
        if ($this->serverHelper->getServerId() != false) {
            $this->binding->getSloBinding()->request('LogoutRequest');
        }
    }

    /**
     * Get all the userdata from the connection source
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * Persist the session information
     *
     * @return $this
     */
    public function persistSession()
    {
        return $this;
    }

    /**
     * Determine if its a SAML request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function isSAMLResponse(\Magento\Framework\App\RequestInterface $request)
    {
        return (
            $request->getParam('SAMLArt') !== null ||
            $request->getParam('SAMLResponse') !== null ||
            $request->getParam('SAMLRequest') !== null)
            ? true
            : false;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return mixed
     */
    public function handleSAMLResponse(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->getParam('SAMLArt') !== null && $this->serverHelper->getServerId() != false) {
            return $this->binding->getArtifactBinding()->resolveArtifact($request->getParam('SAMLArt'));
        }

        $responseData = ($request->getParam('SAMLResponse') !== null)
            ? $request->getParam('SAMLResponse')
            : $request->getParam('SAMLRequest');

        return $this->response->getResponseService()->decode($responseData);
    }
    
    public function getAttributes($SAMLData)
    {
        return $this->attributeMapper->getAttributes($SAMLData);
    }
    
    public function setUserData($userData)
    {
        $this->userData = $userData;
    }

    /**
     * Resolve artifact data
     *
     * @param $artifactData
     * @return mixed
     */
    public function resolveArtifact($artifactData)
    {
        if ($this->serverHelper->getServerId() != false) {
            return $this->binding->getArtifactBinding()->resolveArtifact($artifactData);
        }

        return false;
    }
}
