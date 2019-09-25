<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab;

class History extends \Magento\Backend\Block\Template
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\History\CollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\Registry                                      $registry
     * @param \Magento\Backend\Block\Widget\Context                            $context
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                                 $helpdeskString
     * @param array                                                            $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Mirasvit\Helpdesk\Helper\StringUtil  $helpdeskString,
        array $data = []
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->helpdeskString = $helpdeskString;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('ticket/edit/tab/history.phtml');
    }

    /**
     * @return object
     */
    public function getTicket()
    {
        return $this->registry->registry('current_ticket');
    }

    /**
     * @return object
     */
    public function getHistoryCollection()
    {
        return $this->historyCollectionFactory->create()
                ->addFieldToFilter('main_table.ticket_id', $this->getTicket()->getId())
                ->setOrder('history_id', 'desc')
            ;
    }

    /**
     * @return \Mirasvit\Helpdesk\Helper\StringUtil
     */
    public function getHelpdeskString()
    {
        return  $this->helpdeskString;
    }
}
