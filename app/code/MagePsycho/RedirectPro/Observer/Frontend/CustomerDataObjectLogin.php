<?php

namespace MagePsycho\RedirectPro\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomerDataObjectLogin implements ObserverInterface
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \MagePsycho\RedirectPro\Model\LogoutRedirectCookie
     */
    protected $logoutRedirectCookie;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \MagePsycho\RedirectPro\Model\LogoutRedirectCookie $logoutRedirectCookie,
        \Magento\Framework\Registry $coreRegistry 
    ) {
        $this->redirectProHelper    = $redirectProHelper;
        $this->customerUrl          = $customerUrl;
        $this->customerSession      = $customerSession;
        $this->catalogSession       = $catalogSession;
        $this->logoutRedirectCookie = $logoutRedirectCookie;
        $this->coreRegistry         = $coreRegistry;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }

        $customer             = $observer->getEvent()->getCustomer();
        $groupId              = $customer && $customer->getGroupId() ? $customer->getGroupId() : null;

        $logoutRedirectionUrl = $this->redirectProHelper->getLogoutRedirectionUrl($groupId);
        $this->catalogSession->setAfterLogoutUrlClrp($logoutRedirectionUrl);
        $this->logoutRedirectCookie->set($logoutRedirectionUrl);

        if ($this->coreRegistry->registry('mp_skip_login_redirection')) {
            return $this;
        }

        if ($this->customerSession->isLoggedIn()) {
            $loginRedirectionUrl = $this->redirectProHelper->getLoginRedirectionUrl($groupId);
            $this->customerSession->setBeforeAuthUrl($loginRedirectionUrl);
        } else {
            $this->customerSession->setBeforeAuthUrl($this->customerUrl->getLoginUrl());
        }

        return  $this;
    }
}