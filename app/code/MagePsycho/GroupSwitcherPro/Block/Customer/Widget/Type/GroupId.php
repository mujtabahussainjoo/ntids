<?php

namespace MagePsycho\GroupSwitcherPro\Block\Customer\Widget\Type;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GroupId extends \MagePsycho\GroupSwitcherPro\Block\Customer\Widget\AbstractWidget
{
    const ATTRIBUTE_GROUP_ID    = 'group_id';
    const TEMPLATE_FILE         = 'MagePsycho_GroupSwitcherPro::customer/widget/type/group_id.phtml';

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
        return $this->_getAttribute(self::ATTRIBUTE_GROUP_ID)
            ? (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_ID)->isVisible()
            : false;
    }

    /**
     * Get is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_getAttribute(self::ATTRIBUTE_GROUP_ID)
            ? (bool)$this->_getAttribute(self::ATTRIBUTE_GROUP_ID)->isRequired()
            : false;
    }

    public function getGroupSelectOptions()
    {
        return $this->groupSwitcherHelper->getGroupSelectOptions();
    }

    public function getGroupSelectHtml($name, $selectedValue = '', $class = '')
    {
        $fieldName      = $this->getFieldName($name);
        $fieldId        = $this->getFieldId($name);
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            [
                'id'    => $fieldId,
                'class' => $class
            ]
        )->setValue(
            $selectedValue
        )->setName(
            $fieldName
        )->setOptions(
            $this->getGroupSelectOptions()
        )->setExtraParams(
            ''
        );
        return $select->getHtml();
    }

}