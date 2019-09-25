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
class CustomerRegisterSuccess implements ObserverInterface
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $coreRegistry    
    ) {
        $this->redirectProHelper = $redirectProHelper;
        $this->customerSession   = $customerSession;
        $this->coreRegistry      = $coreRegistry;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }
        
        $customer = $observer->getEvent()->getCustomer();
        $groupId  = $customer->getGroupId();

        // set flag for skipping login redirection when customer register
        if ($this->coreRegistry->registry('mp_skip_login_redirection')) {
            $this->coreRegistry->unregister('mp_skip_login_redirection');
        }
        $this->coreRegistry->register('mp_skip_login_redirection', true);

        $successUrl = $this->redirectProHelper->getAccountRedirectionUrl($groupId);
        $this->customerSession->setBeforeAuthUrl($successUrl);    
        return $this;
    }
}