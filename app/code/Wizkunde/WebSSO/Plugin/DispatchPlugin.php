<?php

namespace Wizkunde\WebSSO\Plugin;

use GuzzleHttp\Promise\AggregateException;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\App\FrontController;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;

class DispatchPlugin
{
    private $connection;

    private $frontendHelper;
    private $backendHelper;
    private $serverHelper;
    private $mappingHelper;
    private $serverData;
    private $customerSession;
    private $customerUrl;
    private $appUrl;
    private $backendSession;
    private $backendUrl;
    private $responseFactory;
    private $redirectInterface;
    private $logoClass;
    private $cmsRouter;

    /**
     * BeforeDispatch constructor.
     * @param \Wizkunde\WebSSO\Helper\Frontend $ssoHelper
     * @param \Wizkunde\WebSSO\Helper\Backend $backendHelper
     * @param \Wizkunde\WebSSO\Helper\Server $serverHelper
     * @param \Magento\Framework\App\State $mappingHelper
     * @param \Wizkunde\WebSSO\Model\Server $serverData
     * @param \Wizkunde\WebSSO\Connection\Connection $connection
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\Url $appUrl
     * @param \Magento\Backend\Model\Auth\Session $backendSession
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param ResponseFactory $responseFactory
     * @param \Magento\Framework\App\Response\RedirectInterface $redirectInterface
     * @param \Magento\Theme\Block\Html\Header\Logo $logoClass
     * @param \Magento\Cms\Controller\Router $cmsRouter
     */
    public function __construct(
        \Wizkunde\WebSSO\Helper\Frontend $ssoHelper,
        \Wizkunde\WebSSO\Helper\Backend $backendHelper,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\Framework\App\State $mappingHelper,
        \Wizkunde\WebSSO\Model\Server $serverData,
        \Wizkunde\WebSSO\Connection\Connection $connection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Url $appUrl,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Magento\Backend\Helper\Data $backendUrl,
        ResponseFactory $responseFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirectInterface,
        \Magento\Theme\Block\Html\Header\Logo $logoClass,
        \Magento\Cms\Controller\Router $cmsRouter
    ) {

        $this->frontendHelper = $ssoHelper;
        $this->backendHelper = $backendHelper;
        $this->serverHelper = $serverHelper;
        $this->mappingHelper = $mappingHelper;
        $this->serverData = $serverData;
        $this->connection = $connection;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->appUrl = $appUrl;
        $this->backendSession = $backendSession;
        $this->backendUrl = $backendUrl;
        $this->redirectInterface = $redirectInterface;
        $this->responseFactory = $responseFactory;
        $this->logoClass = $logoClass;
        $this->cmsRouter = $cmsRouter;
    }

    /**
     * Check if SSO is required for the frontend or the backend
     *
     * @param FrontController $frontController
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return array
     */
    public function aroundDispatch(FrontController $frontController, \Closure $proceed, RequestInterface $request)
    {
        if ($this->serverHelper->getServerId() != false) {
            // Handle the backend redirection
            if ($this->serverHelper->checkBackendEnabled() === true &&
                $this->mappingHelper->getAreaCode() === \Magento\Framework\App\Area::AREA_ADMINHTML
            ) {
                if ($this->backendSession->isLoggedIn() === false) {
                    if ($this->connection->getConnection()->isSAMLResponse($request)) {
                        $username = $this->connection->getConnection()->loginBySso($request);
                    } else {
                        $connection = $this->connection->authenticate();
                        $this->backendHelper->loginAdminUser($connection);
                    }

                    header('Location: ' . $this->backendUrl->getHomePageUrl());exit;
                }
            }
        }

        $proceedData = $proceed($request);

        if ($this->serverHelper->getServerId() != false) {
            // Handle the frontend redirection and firewall
            if ($this->serverHelper->checkFrontendEnabled() === true &&
                $this->mappingHelper->getAreaCode() === \Magento\Framework\App\Area::AREA_FRONTEND &&
                $this->customerSession->isLoggedIn() === false
            ) {
                if ($request->getModuleName() == 'Wizkunde_WebSSO' || $this->serverHelper->checkForcedLogin() == false) {
                    return $proceedData;
                }

                // Trigger redirection
                if ($this->serverHelper->isWhitelisted() === false) {
                    header('Location: sso/account/login');exit;
                }
            }
        }

        return $proceedData;
    }

}
