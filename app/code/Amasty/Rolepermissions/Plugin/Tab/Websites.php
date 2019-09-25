<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Tab;

class Websites
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    public function __construct(\Amasty\Rolepermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetWebsiteCollection(
        \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Websites $subject,
        \Magento\Store\Model\ResourceModel\Website\Collection $result
    ) {
        $rule = $this->helper->currentRule();

        if ($rule->getScopeStoreviews() || $rule->getScopeWebsites()) {
            foreach ($result as $ws) {
                $accessible = in_array($ws->getId(), $rule->getPartiallyAccessibleWebsites());

                if (!$accessible) {
                    $result->removeItemByKey($ws->getId());
                }
            }
        }

        return $result;
    }
}
