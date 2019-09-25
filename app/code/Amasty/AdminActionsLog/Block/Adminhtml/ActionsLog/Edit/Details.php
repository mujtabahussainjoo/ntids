<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Details extends Generic
{
    protected $_objectManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setTemplate('Amasty_AdminActionsLog::tab/view/details.phtml');
    }

    public function getLogRows()
    {
        $collection = $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails')->getCollection();
        if (!$this->getEditorElementId())
        {
            return array();
        }
        else
        {
            $collection->addFieldToFilter('log_id', array('in' => $this->getEditorElementId()));
            return $collection;
        }
    }
}
