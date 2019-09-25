<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Backend extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $serverHelper;

    private $user;
    private $authSession;
    private $storeManager;
    private $cookieManager;
    private $adminConfig;
    private $cookieMetadataFactory;
    private $adminSessionsManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\User\Model\User $user,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Backend\Model\Session\AdminConfig $adminConfig,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Security\Model\AdminSessionsManager $adminSessionsManager
    ) {
    
        parent::__construct($context);

        $this->serverHelper = $serverHelper;

        $this->user = $user;
        $this->authSession = $authSession;
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->adminConfig = $adminConfig;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->adminSessionsManager = $adminSessionsManager;
    }

    /**
     * Login the user and create it if it doesnt exist yet
     *
     * @param $connection
     * @return string
     * @throws Zend_Exception
     */
    public function loginAdminUser($connection)
    {
        $userData = $this->serverHelper->getMappings($connection->getUserData());

        $this->user->loadByUsername($userData['email']);

        if (!$this->user->getId()) {
            $this->user->setUsername($userData['email']);
            $this->user->setEmail($userData['email']);
            $this->user->setFirstname($userData['firstname']);
            $this->user->setLastname($userData['lastname']);
            $this->user->setPassword(bin2hex(openssl_random_pseudo_bytes(4)));
            $this->user->setRoleId(1);
            $this->user->save();

            $this->user->loadByUsername($userData['email']);
        }

        // Login the admin user
        $this->authSession->setUser($this->user);
        $this->authSession->processLogin();

        if ($this->authSession->isLoggedIn()) {
            $cookieValue = $this->authSession->getSessionId();
            if ($cookieValue) {
                $cookiePath = str_replace('autologin.php', 'index.php', $this->adminConfig->getCookiePath());
                $cookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($this->adminConfig->getCookieDomain())
                    ->setSecure($this->adminConfig->getCookieSecure())
                    ->setHttpOnly($this->adminConfig->getCookieHttpOnly());
                $this->cookieManager->setPublicCookie($this->authSession->getName(), $cookieValue, $cookieMetadata);

                if (class_exists('Magento\Security\Model\AdminSessionsManager')) {
                    $this->adminSessionsManager->processLogin();
                }
            }
        }
    }
}
