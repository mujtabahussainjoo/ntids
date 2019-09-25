<?php

namespace MagePsycho\RedirectPro\Model\System\Config\Backend\FieldArray;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class NewAccountRedirect extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\FieldArray\NewAccountRedirect
     */
    protected $newAccountRedirectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \MagePsycho\RedirectPro\Helper\FieldArray\NewAccountRedirect $newAccountRedirectHelper,
        array $data = []
    ) {
        $this->newAccountRedirectHelper = $newAccountRedirectHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->newAccountRedirectHelper->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->newAccountRedirectHelper->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}