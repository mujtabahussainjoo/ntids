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

class Messages extends \Magento\Backend\Block\Template
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory
     */
    protected $satisfactionCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory $satisfactionCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                       $config
     * @param \Magento\Framework\Registry                                           $registry
     * @param \Magento\Backend\Block\Widget\Context                                 $context
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                                      $helpdeskString
     * @param array                                                                 $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\CollectionFactory $satisfactionCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        array $data = []
    ) {
        $this->satisfactionCollectionFactory = $satisfactionCollectionFactory;
        $this->config = $config;
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
        $this->setTemplate('ticket/edit/messages.phtml');
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Ticket
     */
    public function getTicket()
    {
        return $this->registry->registry('current_ticket');
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Message[]|\Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection
     */
    public function getMessages()
    {
        return $this->getTicket()->getMessages(true);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message $message
     * @return string
     */
    public function getSourceUrl($message)
    {
        return $this->getUrl('*/*/source', ['message_id' => $message->getId()]);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Message $message
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\Collection|\Mirasvit\Helpdesk\Model\Satisfaction[]
     */
    public function getSatisfactions($message)
    {
        $collection = $this->satisfactionCollectionFactory->create()
            ->addFieldToFilter('message_id', $message->getId());

        return $collection;
    }

    /**
     * @return bool
     */
    public function isShowSatisfactions()
    {
        return $this->config->getSatisfactionIsShowResultsInTicket();
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function getNicetimeSpan($time)
    {
        return $this->helpdeskString->nicetime(strtotime($time));
    }

    /**
     * @param string $time
     *
     * @return string
     */
    public function formatDateTime($time)
    {
        return $this->formatDate(
            $time,
            \IntlDateFormatter::MEDIUM
        ).' '.$this->formatTime(
            $time,
            \IntlDateFormatter::SHORT
        );
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
