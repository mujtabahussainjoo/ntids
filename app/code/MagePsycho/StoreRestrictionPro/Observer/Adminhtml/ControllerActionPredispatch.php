<?php
namespace MagePsycho\StoreRestrictionPro\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Data
     */
    protected $storeRestrictionProHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * ControllerActionPredispatch constructor.
     *
     * @param \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->storeRestrictionProHelper   = $storeRestrictionProHelper;
        $this->messageManager       = $messageManager;
    }

    public function execute(Observer $observer)
    {
        $isValid          = $this->storeRestrictionProHelper->isValid();
        $isActive         = $this->storeRestrictionProHelper->isActive();
        $request          = $observer->getRequest();
        $fullActionName   = $request->getFullActionName();
        if (    $isActive
            && !$isValid
            && 'adminhtml_system_config_edit' == $fullActionName
            && 'magepsycho_storerestrictionpro' == $request->getParam('section')
        ) {
            $this->messageManager->addErrorMessage($this->storeRestrictionProHelper->getMessage());
        }
        return $this;

    }
}