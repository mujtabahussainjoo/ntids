<?php

namespace MagePsycho\RedirectPro\Block\Customer;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Logout extends \Magento\Framework\View\Element\Template
{
    protected $_template  = "MagePsycho_RedirectPro::customer/logout.phtml";

    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var \MagePsycho\RedirectPro\Model\LogoutRedirectCookie
     */
    protected $logoutRedirectCookie;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Catalog\Model\Session $catalogSession,
        \MagePsycho\RedirectPro\Model\LogoutRedirectCookie $logoutRedirectCookie,
        array $data = array()
    ) {
        $this->redirectProHelper    = $redirectProHelper;
        $this->catalogSession       = $catalogSession;
        $this->logoutRedirectCookie = $logoutRedirectCookie;
        parent::__construct($context, $data);
    }

    public function getRedirectionUrl()
    {
        #$redirectionUrl = $this->catalogSession->getAfterLogoutUrlClrp();
        // @todo check why catalogSession doesn't preserve session value after logout
        $redirectionUrl = $this->logoutRedirectCookie->get();
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->redirectProHelper->getConfigHelper()->getDefaultLogoutUrl();
        }

        return $redirectionUrl;
    }

    public function getDelayTime($convert = false)
    {
        $delayTime = $this->redirectProHelper->getConfigHelper()->getLogoutDelay();
        if ($convert) {
            $delayTime = (int) $delayTime * 1000;;
        }
        return $delayTime;
    }

    public function getCustomMessage()
    {
        return $this->redirectProHelper->getCustomLogoutMessage();
    }
}