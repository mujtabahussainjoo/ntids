<?php

namespace MagePsycho\GroupSwitcherPro\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Domain extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    protected $groupSwitcherProHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
    ) {
        $this->groupSwitcherProHelper = $groupSwitcherProHelper;
        parent::__construct($context);
    }


    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $domain = $this->groupSwitcherProHelper->getDomainFromSystemConfig();
        if ($this->groupSwitcherProHelper->hasBundleExtensions()) {
            $domain .= '<script>
require([
    "jquery",
    "domReady!"
], function(jQuery){
	jQuery("#row_magepsycho_groupswitcherpro_general_license_header_start").remove();
	jQuery("#row_magepsycho_groupswitcherpro_general_domain").remove();
	jQuery("#row_magepsycho_groupswitcherpro_general_dev_license").remove();
	jQuery("#row_magepsycho_groupswitcherpro_general_prod_license").remove();
	jQuery("#row_magepsycho_groupswitcherpro_general_domain_type").remove();
	jQuery("#row_magepsycho_groupswitcherpro_general_license_header_end").remove();
});
</script>';
        }
        $element->setValue($domain);

        return $element->getValue();
    }
}