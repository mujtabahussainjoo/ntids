<?php

namespace MagePsycho\RedirectPro\Plugin\Customer\Controller\Account;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Logout
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper
    ) {
        $this->redirectProHelper = $redirectProHelper;
    }

    public function afterExecute(
        \Magento\Customer\Controller\Account\Logout $subject,
        $result
    ) {
        $this->redirectProHelper->log(__METHOD__, true);

        if (
            $this->redirectProHelper->isFxnSkipped()
            || !$this->redirectProHelper->getConfigHelper()->getRemoveLogoutIntermediate()
        ) {
            return $result;
        }

        $logoutRedirectionUrl = $this->redirectProHelper->getLogoutRedirectionUrl();

        // Extract url path
        if ( !empty($logoutRedirectionUrl)) {
            $baseUrl                 = $this->redirectProHelper->getBaseUrl();
            $logoutRedirectionPath   = str_replace(trim($baseUrl, '/'), '', $logoutRedirectionUrl);
            $logoutRedirectionPath   = trim($logoutRedirectionPath, '/');
        } else {
            $logoutRedirectionPath = '/';
        }

        $this->redirectProHelper->log('$logoutRedirectionPath::' . $logoutRedirectionPath);
        $result->setPath($logoutRedirectionPath);
        return $result;
    }
}