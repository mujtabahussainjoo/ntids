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
class ControllerActionPostdispatchNewsletterSubscriberNew implements ObserverInterface
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->redirectProHelper = $redirectProHelper;
        $this->actionFlag        = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }

        $controllerAction = $observer->getEvent()->getControllerAction();
 
        $redirectUrl = $this->redirectProHelper->getNewsletterRedirectionUrl();
        if (empty($redirectUrl)) {
            return $this;
        }

        $this->actionFlag->set('', \Magento\Framework\App\ActionInterface::FLAG_NO_DISPATCH, true);
        $controllerAction->getResponse()->setRedirect($redirectUrl);
        return $this;
    }
}