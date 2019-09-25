<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Store\Model;

class WebsiteRepository
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    public function __construct(\Amasty\Rolepermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetList(
        \Magento\Store\Model\WebsiteRepository $subject,
        $result
    ) {
        $rule = $this->helper->currentRule();

        if ($rule && $rule->getScopeWebsites()) {
            foreach ($result as $key => $website) {
                $websiteId = $website->getId();
                $accessible = in_array($websiteId, $rule->getPartiallyAccessibleWebsites());

                if (!$accessible && $websiteId != 0) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}
