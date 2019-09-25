<?php
namespace Wizkunde\WebSSO\Controller\Account;

use Magento\Framework\Exception\LocalizedException;

class Login extends \Magento\Cms\Controller\Page\View
{
    protected $connection;

    protected $frontendHelper;
    protected $serverHelper;
    protected $mappingHelper;
    protected $customerSession;
    protected $customerUrl;
    protected $appUrl;
    protected $redirectInterface;
    protected $logoClass;
    protected $cmsRouter;
    protected $accountRedirect;

    protected $cookieMetadataManager;
    protected $scopeConfig;
    protected $cookieMetadataFactory;

    protected $pageHelper;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Account\Redirect $accountRedirect,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Wizkunde\WebSSO\Helper\Frontend $ssoHelper,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\Framework\App\State $mappingHelper,
        \Wizkunde\WebSSO\Connection\Connection $connection,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Url $appUrl,
        \Magento\Framework\App\Response\RedirectInterface $redirectInterface,
        \Magento\Theme\Block\Html\Header\Logo $logoClass,
        \Magento\Cms\Controller\Router $cmsRouter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Helper\Page $pageHelper,
        \Wizkunde\WebSSO\Helper\Logger $logger
    ) {
        $this->_customerFactory = $customerFactory;

        $this->frontendHelper = $ssoHelper;
        $this->serverHelper = $serverHelper;
        $this->mappingHelper = $mappingHelper;
        $this->connection = $connection;
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->appUrl = $appUrl;
        $this->redirectInterface = $redirectInterface;
        $this->logoClass = $logoClass;
        $this->cmsRouter = $cmsRouter;
        $this->accountRedirect = $accountRedirect;
        $this->scopeConfig = $scopeConfig;
        $this->pageHelper = $pageHelper;

        $this->logger = $logger;

        parent::__construct($context,$resultForwardFactory);
    }


    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     */
    private function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config\ScopeConfigInterface::class
            );
        } else {
            return $this->scopeConfig;
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }


    public function execute()
    {
        if ($this->serverHelper->checkFrontendEnabled() === true &&
            $this->mappingHelper->getAreaCode() !== \Magento\Framework\App\Area::AREA_ADMINHTML
        ) {
            if ($this->customerSession->isLoggedIn() === false) {
                $action = $this->cmsRouter->match($this->getRequest());

                // If we're we're on the homepage or we're not on a valid CMS page, redirect
                if (!$action || $this->getRequest()->getModuleName() != 'cms' || $this->logoClass->isHomepage()) {
                    $this->customerSession->setAfterAuthUrl($this->redirectInterface->getRefererUrl());

                    try {
                        $username = false;

                        try {
                            if ($this->connection->getConnection() instanceof \Wizkunde\WebSSO\Connection\SAML2\Connection) {
                                if ($this->connection->getConnection()->isSAMLResponse($this->getRequest())) {
                                    $username = $this->connection->getConnection()->loginBySso($this->getRequest());
                                } else {
                                    $connection = $this->connection->authenticate();
                                    $username = $this->frontendHelper->loginUser($connection);
                                }
                            } else {
                                $connection = $this->connection->authenticate();
                                $username = $this->frontendHelper->loginUser($connection);

                                $this->customerSession->setAccessToken($connection->getAccessToken());
                            }
                        } catch (LocalizedException $e)
                        {
                            $this->logger->createLog($this->connection->getUserData(), uniqid(), $e->getMessage(), false);

                            return $this->showErrorPage(
                                __('The login could not be completed due to configuration issues. Please contact the store manager.'),
                                'wizkunde/websso/cms_mapping_error'
                            );
                        }

                        if($username === false)
                        {
                            $this->logger->createLog($this->connection->getUserData(), uniqid(), 'Email, Firstname and Lastname set, but account creation failed', false);

                            return $this->showErrorPage(
                                __('The login could not be completed due to configuration issues. Please contact the store manager.'),
                                'wizkunde/websso/cms_mapping_error'
                            );
                        } else {
                            $this->logger->createLog($this->connection->getUserData(), uniqid(), "Login Successful", true);

                            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                                $metadata->setPath('/');
                                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                            }
                            $redirectUrl = $this->accountRedirect->getRedirectCookie();

                            if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {

                                $this->accountRedirect->clearRedirectCookie();
                                $resultRedirect = $this->resultRedirectFactory->create();
                                // URL is checked to be internal in $this->_redirect->success()
                                $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                                return $resultRedirect;
                            }else{
                                $resultRedirect = $this->resultRedirectFactory->create();
                                $resultRedirect->setPath('/');
                                return $resultRedirect;
                            }
                        }
                    }  catch (\Magento\Framework\Exception\EmailNotConfirmedException $e) {
                        $value = $this->customerUrl->getEmailConfirmationUrl($username);
                        $message = __(
                            'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                            $value
                        );
                        $this->messageManager->addError($message);
                        $this->session->setUsername($username);

                        $this->logger->createLog(array('exception' => $e->getMessage(), 'userdata' => $this->connection->getUserData()), uniqid(), $e->getMessage(), false);

                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('/');
                        return $resultRedirect;

                    } catch (\Magento\Framework\Exception\State\UserLockedException $e) {
                        $message = __(
                            'You did not sign in correctly or your account is temporarily disabled.'
                        );
                        $this->messageManager->addError($message);
                        $this->session->setUsername($username);

                        $this->logger->createLog(array('exception' => $e->getMessage(), 'userdata' => $this->connection->getUserData()), uniqid(), $e->getMessage(), false);

                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('/');
                        return $resultRedirect;
                    } catch (\Magento\Framework\Exception\AuthenticationException $e) {
                        $message = __('You did not sign in correctly or your account is temporarily disabled.');
                        $this->messageManager->addError($message);
                        $this->session->setUsername($username);

                        $this->logger->createLog(array('exception' => $e->getMessage(), 'userdata' => $this->connection->getUserData()), uniqid(), $e->getMessage(), false);

                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('/');
                        return $resultRedirect;
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $message = $e->getMessage();
                        $this->messageManager->addError($message);
                        $this->session->setUsername($username);

                        $this->logger->createLog(array('exception' => $e->getMessage(), 'userdata' => $this->connection->getUserData()), uniqid(), $e->getMessage(), false);

                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('/');
                        return $resultRedirect;
                    } catch (\Exception $e) {
                        // PA DSS violation: throwing or logging an exception here can disclose customer password
                        $this->messageManager->addError(
                            __('An unspecified error occurred. Please contact us for assistance.')
                        );

                        $this->logger->createLog(array('exception' => $e->getMessage(), 'userdata' => $this->connection->getUserData()), uniqid(), $e->getMessage(), false);

                        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('/');
                        return $resultRedirect;
                    }
                }
            } else {
                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('/');
                return $resultRedirect;
            }

            return $this->accountRedirect->getRedirect();
        }
    }

    /**
     * @param $message
     * @param string $errorSettingPath
     *
     * @return mixed
     */
    protected function showErrorPage($message, $errorSettingPath = 'wizkunde/websso/cms_mapping_error')
    {
        $this->messageManager->addError($message);

        $pageUrl = $this->pageHelper->getPageUrl($this->scopeConfig->getValue($errorSettingPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($pageUrl);
        return $resultRedirect;
    }
}
