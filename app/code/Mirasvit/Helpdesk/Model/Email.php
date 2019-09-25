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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Email\Collection|\Mirasvit\Helpdesk\Model\Email[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Email load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Email setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Email setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Email getResource()
 * @method string getSenderName()
 * @method \Mirasvit\Helpdesk\Model\Email setSenderName(string $name)
 * @method bool getIsProcessed()
 * @method \Mirasvit\Helpdesk\Model\Email setIsProcessed(bool $flag)
 * @method int getAttachmentMessageId()
 * @method \Mirasvit\Helpdesk\Model\Email setAttachmentMessageId(int $id)
 * @method string getFromEmail()
 * @method \Mirasvit\Helpdesk\Model\Email setFromEmail(string $email)
 * @method int getGatewayId()
 * @method \Mirasvit\Helpdesk\Model\Email setGatewayId(int $id)
 * @method int getPatternId()
 * @method \Mirasvit\Helpdesk\Model\Email setPatternId(int $id)
 * @method string getHeaders()
 * @method \Mirasvit\Helpdesk\Model\Email setHeaders(string $param)
 * @method string getSubject()
 * @method \Mirasvit\Helpdesk\Model\Email setSubject(string $param)
 * @method int getMailingDate()
 * @method \Mirasvit\Helpdesk\Model\Email setMailingDate(int $param)
 * @method string getBody()
 * @method \Mirasvit\Helpdesk\Model\Email setBody(string $param)
 * @method string getToEmail()
 * @method \Mirasvit\Helpdesk\Model\Email setToEmail(string $param)
 * @method string getCc()
 * @method \Mirasvit\Helpdesk\Model\Email setCc(string $param)
 * @method string getFormat()
 * @method \Mirasvit\Helpdesk\Model\Email setFormat(string $param)
 * @method int getMessageId()
 * @method \Mirasvit\Helpdesk\Model\Email setMessageId(int $param)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 */
class Email extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_email';
    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_email';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_email';

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
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory
     */
    protected $attachmentCollectionFactory;

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
     * @param \Mirasvit\Helpdesk\Model\GatewayFactory                             $gatewayFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory
     * @param \Magento\Framework\Model\Context                                    $context
     * @param \Magento\Framework\Registry                                         $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource             $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb                       $resourceCollection
     * @param array                                                               $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Email');
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
     *
     */
    public function getAttachments()
    {
        return $this->attachmentCollectionFactory->create()
            ->addFieldToFilter('email_id', $this->getId());
    }

    /**
     * @return string
     */
    public function getSenderNameOrEmail()
    {
        if ($this->getSenderName()) {
            return $this->getSenderName();
        }

        return $this->getFromEmail();
    }

    /**
     * @var  \Mirasvit\Helpdesk\Model\Gateway
     */
    protected $gateway = null;

    /**
     * @return bool|null
     */
    public function getGateway()
    {
        if (!$this->getGatewayId()) {
            return false;
        }
        if ($this->gateway === null) {
            $this->gateway = $this->gatewayFactory->create()->load($this->getGatewayId());
        }

        return $this->gateway;
    }

    /**
     * Deletes all attachments linked with current email
     *
     * @return $this
     */
    public function beforeDelete()
    {
        $attachments = $this->attachmentCollectionFactory->create()
                        ->addFieldToFilter('email_id', $this->getId());
        foreach ($attachments as $attachment) {
            $attachment->delete();
        }

        return parent::beforeDelete();
    }
}
