<?php

namespace MagePsycho\RedirectPro\Observer\Adminhtml;

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
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->redirectProHelper   = $redirectProHelper;
        $this->messageManager      = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $isValid          = $this->redirectProHelper->isValid();
        $isActive         = $this->redirectProHelper->isActive();
        $request          = $observer->getRequest();
        $fullActionName   = $request->getFullActionName();
        if ($isActive
            && !$isValid
            && 'adminhtml_system_config_edit' == $fullActionName
            && 'magepsycho_redirectpro' == $request->getParam('section')
        ) {
            $this->messageManager->addErrorMessage($this->redirectProHelper->getMessage());
        }
        return $this;
    }
}