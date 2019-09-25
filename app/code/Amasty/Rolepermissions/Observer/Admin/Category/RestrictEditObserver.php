<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class RestrictEditObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->helper = $helper;
        $this->_authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($id = $observer->getRequest()->getParam('id')) {
            $rule = $this->helper->currentRule();

            if ($rule->getCategories() && !in_array($id, $rule->getCategories())) {
                $this->helper->redirectHome();
            }

            if ($observer->getRequest()->getActionName() == 'delete') {
                if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_categories')) {
                    $this->helper->redirectHome();
                }
            }
        }
    }
}
