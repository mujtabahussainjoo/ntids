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



namespace Mirasvit\Helpdesk\Block\Ticket;

use Mirasvit\Helpdesk\Api\Data\TicketInterface;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Order
     */
    protected $helpdeskOrder;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Field                  $helpdeskField
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Helpdesk\Helper\Order                  $helpdeskOrder
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Helpdesk\Helper\Order $helpdeskOrder,
        array $data = []
    ) {
        $this->helpdeskField = $helpdeskField;
        $this->registry = $registry;
        $this->context = $context;
        $this->helpdeskOrder = $helpdeskOrder;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $ticket = $this->getTicket();
        $this->pageConfig->getTitle()->set(__('['.$ticket->getCode().'] '.$ticket->getSubject()));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__($ticket->getSubject()));
        }
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Ticket
     */
    public function getTicket()
    {
        return $this->registry->registry('current_ticket');
    }

    /**
     * @return View\Summary\DefaultRow[]
     */
    public function getSummary()
    {
        $rows = [];

        $names = array_intersect($this->getChildNames(), $this->getGroupChildNames('summary'));

        foreach ($names as $name) {
            $rows[$name] = $this->getChildBlock($name);
        }

        return $rows;
    }

    public function getSummaryHtml(View\Summary\DefaultRow $row, TicketInterface $item)
    {
        $row->setItem($item);

        return $row->toHtml();
    }

    /**
     *
     */
    public function getPostUrl()
    {
        $ticket = $this->getTicket();
        if ($this->registry->registry('external_ticket')) {
            return $this->context->getUrlBuilder()->getUrl(
                'helpdesk/ticket/postexternal',
                ['id' => $ticket->getExternalId()]
            );
        } else {
            return $this->context->getUrlBuilder()->getUrl('helpdesk/ticket/postmessage', ['id' => $ticket->getId()]);
        }
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getCustomFields()
    {
        $collection = $this->helpdeskField->getVisibleCustomerCollection();

        return $collection;
    }

    /**
     * @return \Mirasvit\Helpdesk\Helper\Order
     */
    public function getHelpdeskData()
    {
        return $this->helpdeskOrder;
    }

    /**
     * @return \Mirasvit\Helpdesk\Helper\Field
     */
    public function getHelpdeskField()
    {
        return $this->helpdeskField;
    }

    /**
     * @return bool
     */
    public function isExternal()
    {
        return $this->getRequest()->getActionName() == 'external';
    }

    /**
     * Escape HTML entities
     *
     * @param string|array $data
     * @param array|null $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        //html can contain incorrect symbols which produce warrnings to log
        $internalErrors = libxml_use_internal_errors(true);
        $res = parent::escapeHtml($data, $allowedTags);
        libxml_use_internal_errors($internalErrors);
        return $res;
    }
}
