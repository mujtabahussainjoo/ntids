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



namespace Mirasvit\Helpdesk\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction\Collection|
 * \Mirasvit\Helpdesk\Model\Satisfaction[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction getResource()
 * @method int getMessageId()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setMessageId(int $messageId)
 * @method int getTicketId()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setTicketId(int $ticketId)
 * @method int getRate()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setRate(int $rate)
 * @method int getCustomerId()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setCustomerId(int $id)
 * @method int getUserId()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setUserId(int $id)
 * @method int getStoreId()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setStoreId(int $id)
 * @method string getComment()
 * @method \Mirasvit\Helpdesk\Model\Satisfaction setComment(string $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 */
class Satisfaction extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_satisfaction';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_satisfaction';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_satisfaction';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Mirasvit\Helpdesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Mirasvit\Helpdesk\Model\MessageFactory                 $messageFactory
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                  $ticketFactory
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\MessageFactory $messageFactory,
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->messageFactory = $messageFactory;
        $this->ticketFactory = $ticketFactory;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Counstruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Satisfaction');
    }

    /**
     * @param string|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     * @var null
     */
    protected $message = null;

    /**
     * @return bool|\Mirasvit\Helpdesk\Model\Message
     */
    public function getMessage()
    {
        if (!$this->getMessageId()) {
            return false;
        }
        if ($this->message === null) {
            $this->message = $this->messageFactory->create()->load($this->getMessageId());
        }

        return $this->message;
    }

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket
     */
    protected $ticket = null;

    /**
     * @return bool|\Mirasvit\Helpdesk\Model\Ticket
     */
    public function getTicket()
    {
        if (!$this->getTicketId()) {
            return false;
        }
        if ($this->ticket === null) {
            $this->ticket = $this->ticketFactory->create()->load($this->getTicketId());
        }

        return $this->ticket;
    }

    /************************/
}
