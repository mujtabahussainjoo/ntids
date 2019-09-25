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

class Other extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var \Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid $grid
     */
    protected $grid;

    /**
     * @return object
     */
    public function getTicket()
    {
        return $this->registry->registry('current_ticket');
    }

    /**
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $ticket = $this->getTicket();

        /** @var \Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid $grid */
        $grid = $this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid');
        $this->grid = $grid;
        $grid->setId('helpdesk_grid_internal');
        $grid->setActiveTab('other');
        $customerCondition = $ticket->getCustomerId() ? ' OR customer_id='.(int) $ticket->getCustomerId() : '';
        $grid->addCustomFilter('(customer_email = "'.addslashes($ticket->getCustomerEmail()).'"'.$customerCondition.')
            AND ticket_id <> '.$ticket->getId());
        $grid->setFilterVisibility(false);
        $grid->setPagerVisibility(0);
        $grid->setTabMode(true);
        $grid->toHtml();// @todo fix it

        return '<div></div>';
    }

    /**
     * @return array|string
     */
    public function getFormattedNumberOfTickets()
    {
        if (!$this->grid) {
            return '';
        }

        return $this->grid->getFormattedNumberOfTickets();
    }
}
