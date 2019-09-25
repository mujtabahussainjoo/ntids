<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\CatalogRule\Block\Adminhtml\Edit;

class GenericButton
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
     * GenericButton constructor.
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     */
    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function afterCanRender($subject, $result)
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $rule */
        $rule = $this->helper->currentRule();
        $catalogPriceRule = $this->registry->registry('catalog_price_rule');
        $removeButtons = [
            'save',
            'save_and_continue_edit',
            'save_apply',
            'delete'
        ];

        if (!$rule->isAttributesInRole($catalogPriceRule, $rule::CATALOG)) {
            if (in_array($result, $removeButtons)) {
                $result = false;
            }
        }

        return $result;
    }
}
