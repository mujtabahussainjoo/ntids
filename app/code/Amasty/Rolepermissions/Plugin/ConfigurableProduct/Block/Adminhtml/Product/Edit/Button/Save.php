<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

namespace Amasty\Rolepermissions\Plugin\ConfigurableProduct\Block\Adminhtml\Product\Edit\Button;

use Magento\Framework\AuthorizationInterface;
use Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Button\Save as ButtonSave;

class Save
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * Save constructor.
     * @param AuthorizationInterface $authorization
     */
    public function __construct(AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * If user has no access to save product, hide save button.
     *
     * @param ButtonSave $object
     * @param array $result
     * @return array
     */
    public function afterGetButtonData(ButtonSave $object, $result)
    {
        if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
            $result = [];
        }

        return $result;
    }
}
