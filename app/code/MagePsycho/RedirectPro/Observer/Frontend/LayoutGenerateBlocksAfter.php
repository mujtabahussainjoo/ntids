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
class LayoutGenerateBlocksAfter implements ObserverInterface
{
    const CUSTOMER_LOGOUT_PAGE_FULL_ACTION  = 'customer_account_logoutSuccess';

    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    public function __construct(
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper,
        \Magento\Catalog\Model\Session $catalogSession    
    ) {
        $this->redirectProHelper    = $redirectProHelper;
        $this->catalogSession       = $catalogSession;
    }

    public function execute(Observer $observer)
    {
        $this->redirectProHelper->log(__METHOD__, true);
        if ($this->redirectProHelper->isFxnSkipped()) {
            return $this;
        }
        
        $fullActionName = $observer->getFullActionName();
        if ($fullActionName != self::CUSTOMER_LOGOUT_PAGE_FULL_ACTION) {
            return $this;
        }

        $layout = $observer->getLayout();
        $layout->unsetElement('customer_logout', true);
        $layout->addBlock(
            'MagePsycho\RedirectPro\Block\Customer\Logout',
            'magepsycho_customer_logout',
            'content',
            'magepsycho.customer.logout'
        );
    }
}