<?php

namespace MagePsycho\GroupSwitcherPro\Model\System\Config\Backend\FieldArray;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GroupCode extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\FieldArray\GroupCode
     */
    protected $groupCodeHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \MagePsycho\GroupSwitcherPro\Helper\FieldArray\GroupCode $groupCodeHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->groupCodeHelper = $groupCodeHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->groupCodeHelper->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->groupCodeHelper->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}