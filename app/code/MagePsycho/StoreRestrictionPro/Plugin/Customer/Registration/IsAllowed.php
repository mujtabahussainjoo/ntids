<?php

namespace MagePsycho\StoreRestrictionPro\Plugin\Customer\Registration;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class IsAllowed
{
    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Data
     */
    protected $storeRestrictionProHelper;

    public function __construct(
        \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper
    ) {
        $this->storeRestrictionProHelper = $storeRestrictionProHelper;
    }

    public function afterIsAllowed(
        \Magento\Customer\Model\Registration $subject,
        $result
    ) {
        if (   $this->storeRestrictionProHelper->isFxnSkipped()
            || $this->storeRestrictionProHelper->isAccountRegistrationDisabled()
        ) {
            return false;
        }
        return true;
    }
}