<?php

namespace MagePsycho\GroupSwitcherPro\Observer\Frontend;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LayoutGenerateBlocksAfter implements ObserverInterface
{
    const TEMPLATE_FOR_CUSTOMER_REGISTER_218    = "MagePsycho_GroupSwitcherPro::customer/form/register-218.phtml";
    const TEMPLATE_FOR_CUSTOMER_REGISTER        = "MagePsycho_GroupSwitcherPro::customer/form/register.phtml";
    const TEMPLATE_FOR_CUSTOMER_EDIT            = "MagePsycho_GroupSwitcherPro::customer/form/edit.phtml";
    const CUSTOMER_REGISTER_PAGE_FULL_ACTION    = 'customer_account_create';
    const CUSTOMER_EDIT_PAGE_FULL_ACTION        = 'customer_account_edit';

    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    protected $groupSwitcherProHelper;

    public function __construct(
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
    ) {
        $this->groupSwitcherProHelper = $groupSwitcherProHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->groupSwitcherProHelper->log(__METHOD__, true);
        if ($this->groupSwitcherProHelper->isFxnSkipped()) {
            return $this;
        }

        $fullActionName = $observer->getFullActionName();

        if (
            ! in_array(
                $fullActionName,
                [
                    self::CUSTOMER_EDIT_PAGE_FULL_ACTION,
                    self::CUSTOMER_REGISTER_PAGE_FULL_ACTION
                ]
            )
        ) {
            return $this;
        }

        $layout = $observer->getLayout();
        if ($fullActionName == self::CUSTOMER_REGISTER_PAGE_FULL_ACTION) {
            $customerRegisterBlock = $layout->getBlock('customer_form_register');

            // Since 2.1.9 formKey was introduced in register.phtml
            if (version_compare($this->groupSwitcherProHelper->getMageVersion(), '2.1.9', '<')) {
                $customerRegisterBlock->setTemplate(self::TEMPLATE_FOR_CUSTOMER_REGISTER_218);
            } else {
                $customerRegisterBlock->setTemplate(self::TEMPLATE_FOR_CUSTOMER_REGISTER);
            }

        } else {
            $customerEditBlock = $layout->getBlock('customer_edit');
            $customerEditBlock->setTemplate(self::TEMPLATE_FOR_CUSTOMER_EDIT);
        }
    }
}