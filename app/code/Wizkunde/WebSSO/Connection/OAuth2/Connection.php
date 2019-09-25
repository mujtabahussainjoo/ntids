<?php

namespace Wizkunde\WebSSO\Connection\OAuth2;

use Magento\Framework\App\RequestInterface;
use Wizkunde\WebSSO\Connection\ConnectionInterface;

class Connection implements ConnectionInterface
{
    private $oauth2Provider = null;
    private $localeResolver;
    private $request;
    private $customerUrl;
    private $userData;

    private $accessToken;

    protected $storeManager;

    protected $customerSession;

    /**
     * Connection constructor.
     * @param Provider $oauth2Provider
     * @param RequestInterface $requestInterface
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Provider $oauth2Provider,
        RequestInterface $requestInterface,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->oauth2Provider = $oauth2Provider;
        $this->localeResolver = $localeResolver;
        $this->request = $requestInterface;
        $this->customerUrl = $customerUrl;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
    }

    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Provider
     */
    private function getOAuth2Provider()
    {
        return $this->oauth2Provider;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return array
     */
    public function getLoginRequest()
    {
        return [
            'protocol' => 'GET',
            'url' => $this->getOAuth2Provider()->getAuthorizationUrl([
                'redirect_uri' =>  rtrim($this->storeManager->getStore()->getUrl('sso/account/login'), '/'),
                'language' => substr($this->localeResolver->getLocale(), 0, 2)
            ])
        ];
    }

    public function prepareLoginBySso(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->getParam('code') != null) {
            $accessToken = $this->getOAuth2Provider()->getAccessToken('authorization_code', [
                'redirect_uri' => rtrim($this->storeManager->getStore()->getUrl('sso/account/login'), '/'),
                'code' => $this->getRequest()->getParam('code')
            ]);

            $this->accessToken = $accessToken;

            $resourceOwner = $this->getOAuth2Provider()->getResourceOwner($accessToken);
            $this->setUserData($resourceOwner->toArray());
        }
    }

    /**
     * Authenticate against the connection source
     *
     * @return mixed
     */
    public function authenticate()
    {
        if ($this->getRequest()->getParam('code') == null) {
            $this->getOAuth2Provider()->authorize([
                'redirect_uri' => rtrim($this->storeManager->getStore()->getUrl('sso/account/login'), '/'),
                'language' => substr($this->localeResolver->getLocale(), 0, 2)
            ]);
        } else {
            $accessToken = $this->getOAuth2Provider()->getAccessToken('authorization_code', [
                'redirect_uri' => rtrim($this->storeManager->getStore()->getUrl('sso/account/login'), '/'),
                'code' => $this->getRequest()->getParam('code')
            ]);

            $resourceOwner = $this->getOAuth2Provider()->getResourceOwner($accessToken);
            $this->setUserData($resourceOwner->toArray());
        }

        return $this;
    }

    public function logout()
    {
        if ($this->customerSession->getAccessToken() !== null) {
            $this->getOAuth2Provider()->logout($this->customerSession->getAccessToken());
        }

        return $this;
    }

    private function setUserData(array $userData)
    {
        $this->userData = $userData;
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function persistSession()
    {
        // Not needed with OAuth2
        
        return $this;
    }

    public function getMetadataXml()
    {
        return '';
    }
}
