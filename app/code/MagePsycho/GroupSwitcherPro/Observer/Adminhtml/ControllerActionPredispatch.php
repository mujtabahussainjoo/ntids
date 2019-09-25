<?php

namespace MagePsycho\GroupSwitcherPro\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    protected $groupSwitcherProHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * ControllerActionPredispatch constructor.
     *
     * @param \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->groupSwitcherProHelper   = $groupSwitcherProHelper;
        $this->messageManager       = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $isValid          = $this->groupSwitcherProHelper->isValid();
        $isActive         = $this->groupSwitcherProHelper->isActive();
        $request          = $observer->getRequest();
        $fullActionName   = $request->getFullActionName();
        if ($isActive
            && !$isValid
            && 'adminhtml_system_config_edit' == $fullActionName
            && 'magepsycho_groupswitcherpro' == $request->getParam('section')
        ) {
            $this->messageManager->addError($this->groupSwitcherProHelper->getMessage());
        }
        return $this;

    }
}