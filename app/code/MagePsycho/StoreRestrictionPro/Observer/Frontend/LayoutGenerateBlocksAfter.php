<?php

namespace MagePsycho\StoreRestrictionPro\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LayoutGenerateBlocksAfter implements ObserverInterface
{
    const TEMPLATE_FOR_CUSTOMER_NEW          = "MagePsycho_StoreRestrictionPro::customer/newcustomer.phtml";
    const CUSTOMER_LOGIN_PAGE_FULL_ACTION    = 'customer_account_login';

    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Data
     */
    protected $storeRestrictionProHelper;

    public function __construct(
        \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper
    ) {
        $this->storeRestrictionProHelper = $storeRestrictionProHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->storeRestrictionProHelper->log(__METHOD__, true);
        if ( $this->storeRestrictionProHelper->isFxnSkipped()
            || ! $this->storeRestrictionProHelper->shouldShowRegistrationDisabledMessage()
        ) {
            return $this;
        }

        $fullActionName = $observer->getFullActionName();

        if ( ! in_array(
                $fullActionName,
                [
                    self::CUSTOMER_LOGIN_PAGE_FULL_ACTION
                ]
            )
        ) {
            return $this;
        }

        $layout = $observer->getLayout();
        if ($customerNewBlock = $layout->getBlock('customer.new')) {
            $customerNewBlock->setTemplate(self::TEMPLATE_FOR_CUSTOMER_NEW);
        }
    }
}