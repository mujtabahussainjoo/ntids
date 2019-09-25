<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Plugin\User\Controller\Adminhtml\User;

class EditPlugin
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Registry $registry
    ) {
        $this->helper = $helper;
        $this->registry = $registry;
    }

    public function beforeDispatch(
        \Magento\User\Controller\Adminhtml\User\Edit $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        /** @var \Amasty\Rolepermissions\Model\Rule $currentRule */
        $currentRule = $this->helper->currentRule();
        if (!$this->registry->registry('its_amrolepermissions')
            && $currentRule
            && $currentRule->getRoles()
        ) {
            $id = $request->getParam('user_id');
            if ($id) {
                $allowedUsers = $currentRule->getAllowedUsers();
                if (!in_array($id, $allowedUsers)) {
                    $this->helper->redirectHome();
                }
            }
        }

        return [$request];
    }
}
