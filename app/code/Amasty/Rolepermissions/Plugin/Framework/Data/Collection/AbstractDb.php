<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Framework\Data\Collection;

class AbstractDb
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data\Proxy $helper
     */
    private $helper;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data\Proxy $helper
    ) {
        $this->helper = $helper;
    }

    public function beforeGetSize($subject)
    {
        if ($subject instanceof \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection) {
            $restrictedAttributeIds = $this->helper->getRestrictedAttributeIds();

            if ($restrictedAttributeIds) {
                $restrictedSetIds = $this->helper->getRestrictedSetIds();
                $subject->addFieldToFilter('attribute_set_id', ['nin' => $restrictedSetIds]);
            }
        }
    }

    public function afterGetData($subject, $result)
    {
        if ($subject instanceof \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection) {
            if (empty($result)) {
                $result[] = ['value' => '', 'label' => ''];
            }
        }

        return $result;
    }

    public function beforeGetData($subject)
    {
        if ($subject instanceof \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection) {
            $restrictedAttributeIds = $this->helper->getRestrictedAttributeIds();

            if ($restrictedAttributeIds) {
                $restrictedSetIds = $this->helper->getRestrictedSetIds();
                $subject->addFieldToFilter('attribute_set_id', ['nin' => $restrictedSetIds]);
            }
        }
    }
}
