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

class Additional extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\Channel
     */
    protected $helpdeskChannel;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Channel     $helpdeskChannel
     * @param \Magento\Framework\Data\FormFactory   $formFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Channel $helpdeskChannel,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->helpdeskChannel = $helpdeskChannel;
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
        $ticket = $this->registry->registry('current_ticket');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('Additional Information')]);
        if ($ticket->getId()) {
            $fieldset->addField('ticket_id', 'hidden', [
                'name' => 'ticket_id',
                'value' => $ticket->getId(),
            ]);
        }
        if ($ticket->getId()) {
            $fieldset->addField('subject', 'text', [
                'label' => __('Subject'),
                'name' => 'subject',
                'value' => $ticket->getSubject(),
            ]);
        }
        $fieldset->addField('store_label', 'label', [
            'name' => 'store_label',
            'label' => __('Store View'),
            'title' => __('Store View'),
            'value' => $ticket->getStore()->getName(),
        ]);

        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => $ticket->getStoreId(),
        ]);

        $element = $fieldset->addField('channel', 'label', [
            'label' => __('Channel'),
            'name' => 'channel',
            'value' => $this->helpdeskChannel->getLabel($ticket->getChannel()),
        ]);
        $data = $ticket->getChannelData();
        if (isset($data['url'])) {
            $element->setAfterElementHtml(
                "&nbsp;<a id='view_customer_link' href='".$data['url']."' target='_blank'>".__('open page').'</a>'
            );
        }
        if ($ticket->getId()) {
            $fieldset->addField('external_link', 'link', [
                'label' => __('External Link'),
                'name' => 'external_link',
                'class' => 'external-link',
                'value' => $ticket->getExternalUrl(),
                'href' => $ticket->getExternalUrl(),
                'target' => '_blank',
            ]);
        }
        $tags = [];
        foreach ($ticket->getTags() as $tag) {
            $tags[] = $tag->getName();
        }
        $fieldset->addField('tags', 'text', [
            'label' => __('Tags'),
            'name' => 'tags',
            'value' => implode(', ', $tags),
            'note' => __('comma-separated list'),
        ]);

        return parent::_prepareForm();
    }

    /**
     *
     */
    protected function _toHtml()
    {
        $history = $this->getLayout()->createBlock(
            '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\History'
        )->toHtml();

        return parent::_toHtml().$history;
    }
}
