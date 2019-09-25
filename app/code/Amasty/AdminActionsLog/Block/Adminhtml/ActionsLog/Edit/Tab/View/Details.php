<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Edit\Tab\View;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;


class Details extends Generic implements TabInterface
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
        return __('Modifications Breakdown');
    }

    public function getTabTitle()
    {
        return __('Modifications Breakdown');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getLogRows()
    {
        $collection = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails')->getCollection();
        $registryLog = $this->_coreRegistry->registry('amaudit_actionslog');
        if (!$registryLog)
        {
            return array();
        }
        else
        {
            $collection->addFieldToFilter('log_id', array('in' => $registryLog->getId()));
            return $collection;
        }
    }
}
