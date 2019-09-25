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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\History\Collection|\Mirasvit\Helpdesk\Model\History[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\History load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\History setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\History setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\History getResource()
 * @method int getTicketId()
 * @method \Mirasvit\Helpdesk\Model\History setTicketId(int $ticketId)
 * @method string getMessage()
 * @method \Mirasvit\Helpdesk\Model\History setMessage(string $param)
 * @method string getTriggeredBy()
 * @method \Mirasvit\Helpdesk\Model\History setTriggeredBy(string $param)
 * @method string getName()
 * @method \Mirasvit\Helpdesk\Model\History setName(string $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 */
class History extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_history';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_history';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_history';

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
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                  $ticketFactory
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\History');
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


    /**
     * @param string $text
     * @return $this
     */
    public function addMessage($text)
    {
        if (is_array($text)) {
            $text = implode("\n", $text);
        }
        $this->setMessage(trim($this->getMessage()."\n".$text));
        if ($this->getMessage()) {
            $this->save();
        };

        return $this;
    }

    /************************/
}
