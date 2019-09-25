<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Eav\Model\Entity;

class AbstractEntity
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data $helper
     */
    private $helper;

    /**
     * AbstractEntity constructor.
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     */
    public function __construct(\Amasty\Rolepermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetAttributesByCode($subject, $result)
    {
        $currentRule = $this->helper->currentRule();

        if ($currentRule
            && $currentRule->getAttributes()
        ) {
            $allowedCodes = $this->helper->getAllowedAttributeCodes();

            if (is_array($allowedCodes)) {
                foreach ($result as $key => $value) {
                    if (!in_array($key, $allowedCodes)) {
                        unset($result[$key]);
                    }
                }
            }
        }

        return $result;
    }
}
