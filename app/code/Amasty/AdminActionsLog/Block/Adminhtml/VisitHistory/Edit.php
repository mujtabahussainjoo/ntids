<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\VisitHistory;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_registryManager;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_registryManager = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_visitHistory';
        $this->_blockGroup = 'Amasty_AdminActionsLog';

        parent::_construct();

        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('save');
    }
}
