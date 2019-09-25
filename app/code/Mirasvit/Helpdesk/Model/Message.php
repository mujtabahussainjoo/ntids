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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Message\Collection|\Mirasvit\Helpdesk\Model\Message[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Message load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Message setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Message setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Message getResource()
 * @method int getTicketId()
 * @method \Mirasvit\Helpdesk\Model\Message setTicketId(int $ticketId)
 * @method int getUserId()
 * @method \Mirasvit\Helpdesk\Model\Message setUserId(int $userId)
 * @method string getType()
 * @method \Mirasvit\Helpdesk\Model\Message setType(string $type)
 * @method string getBody()
 * @method \Mirasvit\Helpdesk\Model\Message setBody(string $body)
 * @method string getBodyFormat()
 * @method \Mirasvit\Helpdesk\Model\Message setBodyFormat(string $format)
 * @method \Mirasvit\Helpdesk\Model\Message setTriggeredBy(string $by)
 * @method int getCustomerId()
 * @method \Mirasvit\Helpdesk\Model\Message setCustomerId(int $id)
 * @method string getCustomerName()
 * @method \Mirasvit\Helpdesk\Model\Message setCustomerName(string $name)
 * @method string getCustomerEmail()
 * @method \Mirasvit\Helpdesk\Model\Message setCustomerEmail(string $email)
 * @method bool getIsRead()
 * @method \Mirasvit\Helpdesk\Model\Message setIsRead(boolean $flag)
 * @method string getThirdPartyEmail()
 * @method \Mirasvit\Helpdesk\Model\Message setThirdPartyEmail(string $email)
 * @method string getThirdPartyName()
 * @method \Mirasvit\Helpdesk\Model\Message setThirdPartyName(string $name)
 * @method int getEmailId()
 * @method \Mirasvit\Helpdesk\Model\Message setEmailId(int $id)
 * @method string getUserName()
 * @method \Mirasvit\Helpdesk\Model\Message setUserName(string $name)
 * @method string getUid()
 * @method \Mirasvit\Helpdesk\Model\Message setUid(string $param)
 * @method string getCreatedAt()
 * @method \Mirasvit\Helpdesk\Model\Message setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method \Mirasvit\Helpdesk\Model\Message setUpdatedAt(string $param)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Message extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_message';

    /**
     * @var bool
     */
    public $isNew;

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_message';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_message';

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
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\CollectionFactory $attachmentCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Email\CollectionFactory $emailCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Core\Api\TextHelperInterface $mstcoreString,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ticketFactory               = $ticketFactory;
        $this->userFactory                 = $userFactory;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->emailCollectionFactory      = $emailCollectionFactory;
        $this->config                      = $config;
        $this->mstcoreString               = $mstcoreString;
        $this->helpdeskString              = $helpdeskString;
        $this->variableProcessor           = $filterProvider;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Message');
    }

    /**
     * @param bool|false $emptyOption
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
     * @var null
     */
    protected $user = null;

    /**
     * @return bool|\Magento\User\Model\User
     */
    public function getUser()
    {
        if (!$this->getUserId()) {
            return false;
        }
        if ($this->user === null) {
            $this->user = $this->userFactory->create()->load($this->getUserId());
        }

        return $this->user;
    }


    /**
     * @return  \Mirasvit\Helpdesk\Model\ResourceModel\Attachment\Collection|
     * \Mirasvit\Helpdesk\Model\Attachment    $getAttachments
     */
    public function getAttachments()
    {
        return $this->attachmentCollectionFactory->create()
            ->addFieldToFilter('message_id', $this->getId());
    }

    /**
     * @return string
     */
    public function getFrontendUserName()
    {
        $departments = $this->departmentCollectionFactory->create()
            ->addUserFilter($this->getUserId());
        if ($departments->count()) {
            $department = $departments->getFirstItem();
        } else {
            $department = $this->getTicket()->getDepartment();
        }

        if ($this->config->getGeneralSignTicketBy() == Config::SIGN_TICKET_BY_DEPARTMENT) {
            return $department->getName();
        } else {
            if ($department) {
                return $this->getUserName().' ('.$department->getName().')';
            } else {
                return $this->getUserName();
            }
        }
    }

    /**
     *
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if (!$this->getUid()) {
            $uid = md5(
                (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT).
                $this->mstcoreString->generateRandHeavy(100)
            );
            $this->setUid($uid);
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDelete()
    {
        $attachments = $this->attachmentCollectionFactory->create()
            ->addFieldToFilter('message_id', $this->getId());
        foreach ($attachments as $attachment) {
            $attachment->delete();
        }
        $emails = $this->emailCollectionFactory->create()
            ->addFieldToFilter('message_id', $this->getId());
        foreach ($emails as $email) {
            $email->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @return bool
     */
    public function isThirdParty()
    {
        return $this->getType() == Config::MESSAGE_PUBLIC_THIRD
                    || $this->getType() == Config::MESSAGE_INTERNAL_THIRD;
    }

    /**
     * @return bool
     */
    public function isInternal()
    {
        return $this->getType() == Config::MESSAGE_INTERNAL;
    }

    /**
     * We need this method to support DB from old releases.
     *
     * @return string
     */
    public function getTriggeredBy()
    {
        if ($this->getData('triggered_by')) {
            return $this->getData('triggered_by');
        }
        if ($this->getUser()) {
            return Config::USER;
        }

        return Config::CUSTOMER;
    }


    public function getAllowedTags()
    {
        return ['br', 'p', 'strong', 'a', 'i', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6']; //DONT CHANGE THIS
    }

    /**
     * Return body in HTML format.
     *
     * @return string
     */
    public function getUnsafeBodyHtml()
    {
        $html = $this->getBody();

        if (!$this->isBodyHtml()) {
            $html = $this->helpdeskString->convertToHtml($html);
        }

        // if admin sends something like:
        //   {{block class="Mirasvit\\Rma\\Block\\Rma\\View\\Items" area='frontend'}}  - some text
        // we don't want to parse it in plain mode
        if ($this->config->getGeneralIsWysiwyg()) {
            // Parse WYSIWYG variables, if there's any
            $html = $this->variableProcessor->getPageFilter()->filter($html);
        }

        $html = $this->parseVars($html);

        return $html;
    }

    /**
     * Return body in Plain text format.
     *
     * @return string
     */
    public function getBodyPlain()
    {
        return strip_tags($this->getUnsafeBodyHtml());
    }

    /**
     * Is body saved in DB in html?
     *
     * @return bool
     */
    public function isBodyHtml()
    {
        if ($this->getBodyFormat() == Config::FORMAT_PLAIN) {
            return false;
        }
        if ($this->getBodyFormat() == Config::FORMAT_HTML) {
            return true;
        }
        $tags = ['<div ', '<p ', 'href=', '</p>', '</div>', '</a>', '<br>', '</br>', '<img'];
        foreach ($tags as $tag) {
            if (strpos($this->getBody(), $tag) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $text
     * @return string
     */
    protected function parseVars($text)
    {
        $match = [];
        if (preg_match_all('/\[\[(.*)\]\]/', $text, $match)) {
            foreach ($match[1] as $k => $variable) {
                $parts = explode('__', $variable);
                $parts[0];
                switch ($parts[0]) {
                    case 'ticket_url':
                        $ticket = $this->getTicket();
                        if (!empty($parts[1])) {
                            $ticket = $this->ticketFactory->create()->load($parts[1]);
                        }
                        $url = $this->getIsBackend() ? $ticket->getBackendUrl() : $ticket->getUrl();
                        $text = str_replace(
                            $match[0][$k], '<a href="'.$url.'">'.$ticket->getSubject().'</a>', $text
                        );
                        break;
                }
            }
        }

        return $text;
    }

    /************************/
}
