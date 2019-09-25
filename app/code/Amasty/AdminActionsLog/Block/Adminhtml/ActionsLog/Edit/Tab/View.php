<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Edit\Tab;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;


class View extends Generic implements TabInterface
{
    protected $_objectManager;


    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Item Information');
    }

    public function getTabTitle()
    {
        return __('Item Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getLog()
    {
        $log = $this->_coreRegistry->registry('amaudit_actionslog');
        return $log;
    }

    public function getUser($username)
    {
        $user = $this->_objectManager->get('Magento\User\Model\User')->loadByUsername($username);

        return $user;
    }
}
