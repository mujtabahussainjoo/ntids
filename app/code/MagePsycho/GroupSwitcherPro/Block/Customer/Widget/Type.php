<?php

namespace MagePsycho\GroupSwitcherPro\Block\Customer\Widget;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Type extends \MagePsycho\GroupSwitcherPro\Block\Customer\Widget\AbstractWidget
{
    public function _toHtml()
    {
        if ($this->groupSwitcherHelper->isFxnSkipped()) {
            return '';
        }

        $groupSelectorType  = $this->getConfigHelper()->getGroupSelectionType();

        //@todo Make setMethod() workable directly from .phtml
        if ($groupSelectorType == \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\SelectorType::SELECTOR_TYPE_GROUP_CODE) {
            return $this->getLayout()
                        ->createBlock('MagePsycho\GroupSwitcherPro\Block\Customer\Widget\Type\GroupCode')
                        ->setObject($this->getObject())
                        ->setIsEditPage($this->getIsEditPage())
                        ->setFieldIdFormat($this->getFieldIdFormat())
                        ->setFieldNameFormat($this->getFieldNameFormat())
                        ->toHtml();
        } else if ($groupSelectorType == \MagePsycho\GroupSwitcherPro\Model\System\Config\Source\SelectorType::SELECTOR_TYPE_DROPDOWN) {
            return $this->getLayout()
                        ->createBlock('MagePsycho\GroupSwitcherPro\Block\Customer\Widget\Type\GroupId')
                        ->setObject($this->getObject())
                        ->setIsEditPage($this->getIsEditPage())
                        ->setFieldIdFormat($this->getFieldIdFormat())
                        ->setFieldNameFormat($this->getFieldNameFormat())
                        ->toHtml();
        }
        return '';
    }

}