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



namespace Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
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
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @param \Magento\Framework\Registry              $registry
     * @param \Magento\Backend\Block\Widget\Context    $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Event\Manager $eventManager,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->context = $context;
        $this->jsonEncoder = $jsonEncoder;
        $this->authSession = $authSession;
        $this->eventManager = $eventManager;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('hdmx__ticket-tabs');
        $this->setDestElementId('edit_form');
    }

    /**
     * @return string
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $ticket = $this->registry->registry('current_ticket');
        if ($ticket && !$ticket->getStoreId()) {
            $this->addTab('store', [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Store'
                )->toHtml(),
            ]);

            return parent::_beforeToHtml();
        }

        $this->addTab('general_section', [
            'label' => __('General'),
            'title' => __('General'),
            'content' => $this->getLayout()->createBlock(
                '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\General'
            )->toHtml(),
        ]);
        $this->addTab('additional_section', [
            'label' => __('Additional'),
            'title' => __('Additional'),
            'content' => $this->getLayout()->createBlock(
                '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Additional'
            )->toHtml(),
        ]);
        $this->addTab('followup_section', [
            'label' => __('Follow Up'),
            'title' => __('Follow Up'),
            'content' => $this->getLayout()->createBlock(
                '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Followup'
            )->toHtml(),
        ]);

        if ($ticket && $ticket->getId()) {
            /** @var \Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Other $otherBlock */
            $otherBlock = $this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Other');
            $otherBlockHtml = $otherBlock->toHtml();
            $ticketsNumber = $otherBlock->getFormattedNumberOfTickets();
            $this->addTab('other', [
                'label' => __('Other Tickets (%1)', $ticketsNumber),
                'title' => __('Other Tickets (%1)', $ticketsNumber),
                'content' => $otherBlockHtml,
                'class' => 'other-grid-tab'
            ]);
        }

        $this->eventManager->dispatch('helpdesk_ticket_edit_before_html', [
            'ticket'=>$ticket,
            'tabs'=>$this]);

        return parent::_beforeToHtml();
    }

    /************************/
}
