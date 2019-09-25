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
class ControllerActionPostdispatchNewsletterSubscriberConfirm implements ObserverInterface
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var ControllerActionPostdispatchNewsletterSubscriberNew
     */
    private $controllerActionPostdispatchNewsletterSubscriberNew;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \MagePsycho\RedirectPro\Observer\Frontend\ControllerActionPostdispatchNewsletterSubscriberNew $controllerActionPostdispatchNewsletterSubscriberNew
    ) {
        $this->redirectProHelper                                   = $redirectProHelper;
        $this->controllerActionPostdispatchNewsletterSubscriberNew = $controllerActionPostdispatchNewsletterSubscriberNew;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }
        $this->controllerActionPostdispatchNewsletterSubscriberNew->execute($observer);
    }
}