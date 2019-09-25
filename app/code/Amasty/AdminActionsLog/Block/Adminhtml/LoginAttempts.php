<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml;

class LoginAttempts extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = [])
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_loginAttempts';
        $this->_blockGroup = 'Amasty_AdminActionsLog';
        $this->removeButton('add');

        $script = "
            if (confirm('".__('Are you sure?')."'))
                window.location.href='".$this->getUrl('amaudit/loginattempts/clear')."';
        ";

        $this->addButton('clear', array(
            'label' => __('Clear Log'),
            'onclick' => $script,
            'class' => 'primary',
        ));
    }
}
