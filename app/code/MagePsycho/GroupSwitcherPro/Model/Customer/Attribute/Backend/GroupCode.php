<?php

namespace MagePsycho\GroupSwitcherPro\Model\Customer\Attribute\Backend;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GroupCode extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    protected $groupSwitcherProHelper;

    public function __construct(
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
    ) {
        $this->groupSwitcherProHelper= $groupSwitcherProHelper;
    }   

    public function checkValidate($object)
    {
        $value = $object->getData(
            $this->getAttribute()->getAttributeCode()
        );

        if ($this->groupSwitcherProHelper->skipGroupCodeSelectorFxn()
            || empty($value)
        ) {
            return parent::validate($object);
        }

        if ($matchedGroupId = $this->groupSwitcherProHelper->checkIfGroupCodeIsValid($value)) {
            $object->setGroupId($matchedGroupId);
        } else {
            $message = $this->groupSwitcherProHelper->getConfigHelper()->getGroupCodeErrorMessage();
            if (empty($message)) {
                $message = sprintf('Group code %1 is not valid.', $value);
            }
            throw new \Magento\Framework\Exception\LocalizedException(
                __($message)
            );
        }

        return true;
    }

    public function beforeSave($object)
    {
        $this->checkValidate($object);
        return parent::beforeSave($object);
    }
}