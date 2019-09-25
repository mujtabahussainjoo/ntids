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
class ControllerActionPostdispatchCustomerAccountLogout implements ObserverInterface
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->redirectProHelper = $redirectProHelper;
        $this->storeManager      = $storeManager;
        $this->customerSession   = $customerSession;
        $this->messageManager    = $messageManager;
        $this->actionFlag        = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);

        if (!$this->redirectProHelper->getConfigHelper()->getRemoveLogoutIntermediate()) {
            return $this;
        }

        $controllerAction = $observer->getEvent()->getControllerAction();
        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
        $controllerAction->getResponse()
            ->setRedirect(
                $this->redirectProHelper->getLogoutRedirectionUrl()
            )
        ;
        return $this;
    }
}