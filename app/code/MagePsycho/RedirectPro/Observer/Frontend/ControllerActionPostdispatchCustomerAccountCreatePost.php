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
class ControllerActionPostdispatchCustomerAccountCreatePost implements ObserverInterface
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

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->redirectProHelper = $redirectProHelper;
        $this->storeManager      = $storeManager;
        $this->customerSession   = $customerSession;
        $this->messageManager    = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }

        $successMessage = $this->redirectProHelper->getAccountSuccessMessage();
        if ( !empty($successMessage)) {
            $this->messageManager->getMessages(true);
            $this->messageManager->addSuccessMessage($successMessage);
        }
        return $this;
    }
}