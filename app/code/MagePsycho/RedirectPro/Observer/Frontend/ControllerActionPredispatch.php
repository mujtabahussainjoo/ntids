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
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder    
    ) {
        $this->redirectProHelper = $redirectProHelper;
        $this->customerSession   = $customerSession;
        $this->storeManager      = $storeManager;
        $this->urlBuilder        = $urlBuilder;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }

        $request            = $observer->getEvent()->getRequest();
        $moduleName         = $request->getModuleName();
        $controllerName     = $request->getControllerName();
        $fullActionName     = $request->getFullActionName();

        if ($moduleName != 'customer' 
            && $controllerName != 'account'
        ) {
            if (!in_array(
                    $fullActionName,
                    ['cms_index_noRoute', 'cms_index_defaultNoRoute']
                )
                && !$request->isXmlHttpRequest()
            ) {
                $currentUrl = $this->urlBuilder->getCurrentUrl();
                $this->customerSession->setBeforeAuthUrlClrp($currentUrl);
                $this->redirectProHelper->log('setBeforeAuthUrlClrp::' . $currentUrl);
            }
        }
        
        if ($redirectToParamUrl = $this->redirectProHelper->getRedirectToParamUrl()) {
            $this->customerSession->setRedirectParamUrl($redirectToParamUrl);
            $this->redirectProHelper->log('setRedirectToUrlClrp::' . $redirectToParamUrl);
        }
        
        return $this;
    }
}