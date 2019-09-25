<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Rule\Model\Condition;

class Combine
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data $helper
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    private $registry;

    /**
     * Combine constructor.
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function beforeLoadArray($subject, $arr, $key = 'conditions')
    {
        $currentRule = $this->helper->currentRule();
        if (!$this->registry->registry('its_amrolepermissions')
            && $currentRule
            && $currentRule->getAttributes()
        ) {
            $arr = $this->_removeUnnecessaryAttr($arr);
            $arr = $this->_removeEmptyCombines($arr);
        }

        return [$arr, $key];
    }

    private function _removeUnnecessaryAttr($arr)
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $rule */
        $allowedAttributes = $this->helper->getAllowedAttributeCodes();

        if (isset($arr['conditions'])) {
            foreach ($arr['conditions'] as $key => $value) {
                if ($value['type'] == 'Magento\CatalogRule\Model\Rule\Condition\Product'
                    || $value['type'] == 'Magento\SalesRule\Model\Rule\Condition\Product') {
                    if (is_array($allowedAttributes) && !in_array($value['attribute'], $allowedAttributes)) {
                        unset($arr['conditions'][$key]);
                    }
                } elseif ($value['type'] == 'Magento\CatalogRule\Model\Rule\Condition\Combine'
                    || $value['type'] == 'Magento\SalesRule\Model\Rule\Condition\Product\Subselect'
                ) {
                    $arr['conditions'][$key] = $this->_removeUnnecessaryAttr($arr['conditions'][$key]);
                }
            }
        }

        return $arr;
    }

    private function _removeEmptyCombines($arr)
    {
        $combineTypesOuter = [
            'Magento\CatalogRule\Model\Rule\Condition\Combine',
            'Magento\SalesRule\Model\Rule\Condition\Combine',
            'Magento\SalesRule\Model\Rule\Condition\Product\Subselect'
        ];

        $combineTypesInner = [
            'Magento\CatalogRule\Model\Rule\Condition\Combine',
            'Magento\SalesRule\Model\Rule\Condition\Product\Subselect',
            'Magento\SalesRule\Model\Rule\Condition\Product\Found'
        ];

        if (in_array($arr['type'], $combineTypesOuter)) {
            foreach ($arr['conditions'] as $key => $value) {
                if (in_array($arr['type'], $combineTypesInner)) {
                    if (empty($value['conditions'])) {
                        unset($arr['conditions'][$key]);
                    } else {
                        $arr['conditions'][$key] = $this->_removeEmptyCombines($value);
                    }
                }
            }
        }

        return $arr;
    }
}
