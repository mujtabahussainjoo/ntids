<?php

namespace MagePsycho\GroupSwitcherPro\Block\Customer\Widget\Type;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GroupCode extends \MagePsycho\GroupSwitcherPro\Block\Customer\Widget\AbstractWidget
{
    const ATTRIBUTE_GROUP_CODE  = 'mp_group_code';
    const TEMPLATE_FILE         = 'MagePsycho_GroupSwitcherPro::customer/widget/type/group_code.phtml';

    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate(self::TEMPLATE_FILE);
    }

    /**
     * Get is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_getAttribute(self::ATTRIBUTE_GROUP_CODE)
            ? (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_CODE)->isVisible()
            : false;
    }

    /**
     * Get is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->getConfigHelper()->isGroupFieldRequired();
    }
}