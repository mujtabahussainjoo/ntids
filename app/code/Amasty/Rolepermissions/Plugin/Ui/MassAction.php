<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\Ui;

class MassAction
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(\Magento\Framework\AuthorizationInterface $authorization)
    {
        $this->_authorization = $authorization;
    }

    public function afterGetChildComponents(
        \Magento\Ui\Component\MassAction $subject,
        $result
    ) {
        switch ($subject->getContext()->getNamespace()) {
            case 'product_listing':
                if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_products')) {
                    unset($result['delete']);
                }
                if (!$this->_authorization->isAllowed('Amasty_Paction::paction')) {
                    foreach ($result as $key => $action) {
                        $actionConfig = $action->getConfig();
                        if (strpos($actionConfig['type'], 'amasty_') !== false) {
                            unset($result[$key]);
                        }
                    }
                }
                break;
            case 'customer_listing':
                if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_customers')) {
                    unset($result['delete']);
                }
                if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::save_customers')) {
                    unset($result['subscribe'], $result['unsubscribe'], $result['assign_to_group'], $result['edit']);
                }
                break;
        }

        return $result;
    }
}
