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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\Mirasvit\Helpdesk\Model\Ticket[] getCollection()
 * @method $this load(int $id)
 * @method bool getIsMassDelete()
 * @method $this setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method $this setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Ticket getResource()
 * @method int[] getTagIds()
 * @method $this setTagIds(array $ids)
 * @method string getEmailSubjectPrefix()
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Ticket extends \Mirasvit\Helpdesk\Model\TicketBridge implements IdentityInterface
{
    const CACHE_TAG = 'helpdesk_ticket';

    /**
     * @var bool
     */
    public $isNew;

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_ticket';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_ticket';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Api\Service\Ticket\TicketManagementInterface $ticketManagement,
        \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory,
        \Mirasvit\Helpdesk\Model\PriorityFactory $priorityFactory,
        \Mirasvit\Helpdesk\Model\StatusFactory $statusFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Mirasvit\Helpdesk\Model\MessageFactory $messageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Mirasvit\Helpdesk\Model\EmailFactory $emailFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Helper\Notification $helpdeskNotification,
        \Mirasvit\Helpdesk\Helper\History $helpdeskHistory,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Mirasvit\Helpdesk\Helper\Ruleevent $helpdeskRuleevent,
        \Mirasvit\Helpdesk\Helper\Email $helpdeskEmail,
        \Mirasvit\Helpdesk\Helper\Attachment $helpdeskAttachment,
        \Mirasvit\Helpdesk\Helper\Storeview $storeviewHelper,
        \Magento\Framework\Url $urlManager,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ticketManagement = $ticketManagement;
        $this->departmentFactory = $departmentFactory;
        $this->priorityFactory = $priorityFactory;
        $this->statusFactory = $statusFactory;
        $this->userFactory = $userFactory;
        $this->storeFactory = $storeFactory;
        $this->messageFactory = $messageFactory;
        $this->customerFactory = $customerFactory;
        $this->emailFactory = $emailFactory;
        $this->orderFactory = $orderFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->config = $config;
        $this->helpdeskNotification = $helpdeskNotification;
        $this->helpdeskHistory = $helpdeskHistory;
        $this->helpdeskString = $helpdeskString;
        $this->helpdeskRuleevent = $helpdeskRuleevent;
        $this->helpdeskEmail = $helpdeskEmail;
        $this->helpdeskAttachment = $helpdeskAttachment;
        $this->storeviewHelper = $storeviewHelper;
        $this->urlManager = $urlManager;
        $this->backendUrlManager = $backendUrlManager;
        $this->localeDate = $localeDate;
        $this->storeManager = $storeManager;
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
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Ticket');
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
     * @var Department
     */
    protected $department = null;

    /**
     * @return bool|Department
     */
    public function getDepartment()
    {
        if (!$this->getDepartmentId()) {
            return false;
        }
        if ($this->department === null) {
            $this->department = $this->departmentFactory->create()->load($this->getDepartmentId());
        }

        return $this->department;
    }

    /**
     * @var Priority
     */
    protected $priority = null;

    /**
     * @return bool|Priority
     */
    public function getPriority()
    {
        if (!$this->getPriorityId()) {
            return false;
        }
        if ($this->priority === null) {
            $this->priority = $this->priorityFactory->create()->load($this->getPriorityId());
        }

        return $this->priority;
    }

    /**
     * @var Status
     */
    protected $status = null;

    /**
     * @return bool|Status
     */
    public function getStatus()
    {
        if (!$this->getStatusId()) {
            return false;
        }
        if ($this->status === null) {
            $this->status = $this->statusFactory->create()->load($this->getStatusId());
        }

        return $this->status;
    }

    /**
     * @var \Magento\User\Model\User
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
     * @var \Magento\Store\Model\Store
     */
    protected $store = null;

    /**
     * @return bool|\Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->getStoreId() === null) {
            return false;
        }
        if ($this->store === null) {
            $this->store = $this->storeFactory->create()->load($this->getStoreId());
        }

        return $this->store;
    }

    /**
     * @return array
     */
    public function getCc()
    {
        $cc = $this->getData('cc');
        if ($cc) {
            $cc = explode(',', $cc);
            $cc = array_map('trim', $cc);

            return $cc;
        }

        return [];
    }

    /**
     * @return array
     */
    public function getBcc()
    {
        $cc = $this->getData('bcc');
        if ($cc) {
            $cc = explode(',', $cc);
            $cc = array_map('trim', $cc);

            return $cc;
        }

        return [];
    }

    /************************/

    /**
     * @param string                                                               $text
     * @param \Magento\Customer\Model\Customer|\Magento\Framework\DataObject|false $customer
     * @param \Magento\User\Model\User|false                                       $user
     * @param string                                                               $triggeredBy
     * @param string                                                               $messageType
     * @param bool|\Mirasvit\Helpdesk\Model\Email                                  $email
     * @param bool|string                                                          $bodyFormat
     *
     * @return \Mirasvit\Helpdesk\Model\Message
     *
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function addMessage(
        $text,
        $customer,
        $user,
        $triggeredBy,
        $messageType = Config::MESSAGE_PUBLIC,
        $email = false,
        $bodyFormat = false
    ) {
        $message = $this->messageFactory->create()
            ->setTicketId($this->getId())
            ->setType($messageType)
            ->setBody($text)
            ->setBodyFormat($bodyFormat)
            ->setTriggeredBy($triggeredBy);

        if ($triggeredBy == Config::CUSTOMER) {
            $message->setCustomerId($customer->getId());
            $message->setCustomerName($customer->getName());
            $message->setCustomerEmail($customer->getEmail());
            $message->setIsRead(true);

            $this->setLastReplyName($customer->getName());
        } elseif ($triggeredBy == Config::USER) {
            $message->setUserId($user->getId());
            if ($this->getOrigData('department_id') == $this->getData('department_id') &&
                $this->getOrigData('user_id') == $this->getData('user_id')
            ) {
                if ($messageType != Config::MESSAGE_INTERNAL) {
                    $this->setUserId($user->getId());
                    // In case of different departments of ticket and owner, correct department id
                    $departments = $this->departmentCollectionFactory->create();
                    $departments->addUserFilter($user->getId())
                        ->addFieldToFilter('is_active', true);
                    if ($departments->count() && !in_array($this->getDepartmentId(), $departments->getAllIds())) {
                        $this->department = null;
                        $this->setDepartmentId($departments->getFirstItem()->getId());
                    }
                }
            }
            $this->setLastReplyName($user->getName());
            if ($message->isThirdParty()) {
                $message->setThirdPartyEmail($this->getThirdPartyEmail());
            }
        } elseif ($triggeredBy == Config::THIRD) {
            $message->setThirdPartyEmail($this->getThirdPartyEmail());
            if ($email) {
                $this->setLastReplyName($email->getSenderNameOrEmail());
                $message->setThirdPartyName($email->getSenderName());
            }
        }
        if ($email) {
            $message->setEmailId($email->getId());
        }
        //if ticket was closed, then we have new message from customer, we will open it
        if ($triggeredBy != Config::USER) {
            if ($this->isClosed()) {
                $status = $this->statusFactory->create()->loadByCode(Config::STATUS_OPEN);
                $this->setStatusId($status->getId());
            }
            if ($this->getFolder() !== Config::FOLDER_SPAM) {
                $this->setFolder(Config::FOLDER_INBOX);
            }
        }
        $message->save();

        if ($email) {
            $email->setIsProcessed(true)
                ->setAttachmentMessageId($message->getId())
                ->save();
        } else {
            $this->helpdeskAttachment->saveAttachments($message);
        }

        if ($this->getFolder() !== Config::FOLDER_SPAM) {
            if ($this->getReplyCnt() == 0) {
                $this->helpdeskNotification->newTicket($this, $customer, $user, $triggeredBy, $messageType);
            } else {
                $this->helpdeskNotification->newMessage($this, $customer, $user, $triggeredBy, $messageType);
            }
        }

        $this->setReplyCnt($this->getReplyCnt() + 1);
        if (!$this->getFirstReplyAt() && $user) {
            $this->setFirstReplyAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        if ($messageType != Config::MESSAGE_INTERNAL) {
            $this->setLastReplyAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }

        $this->addToSearchIndex($text);
        $this->save();
        $this->helpdeskHistory->addMessage(
            $this,
            $triggeredBy,
            ['customer' => $customer, 'user' => $user, 'email' => $email],
            $messageType
        );

        return $message;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity) @fixme
     *
     * @return void
     */
    protected function updateFields()
    {
        $config = $this->config;
        if (!$this->getPriorityId()) {
            $this->setPriorityId($config->getDefaultPriority());
        }
        if (!$this->getStatusId()) {
            $this->setStatusId($config->getDefaultStatus());
        }
        if (!$this->getCode()) {
            $this->setCode($this->helpdeskString->generateTicketCode());
        }
        if (!$this->getExternalId()) {
            $this->setExternalId(md5($this->getCode() . $this->helpdeskString->generateRandNum(10)));
        }
        if ($this->getCustomerId() > 0) {
            $customer = $this->customerFactory->create();
            $customer->load($this->getCustomerId());
            //we don't change the email, because customer can send the ticket from another email (not from registered)
            //maybe we don't need this if??
            if (!$this->getCustomerEmail()) {
                $this->setCustomerEmail($customer->getEmail());
            }
            $this->setCustomerName($customer->getName());
        }
        if (!$this->getFirstSolvedAt() && $this->isClosed()) {
            $this->setFirstSolvedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        if (in_array($this->getStatusId(), $config->getGeneralArchivedStatusList()) &&
            $this->getFolder() != Config::FOLDER_SPAM
        ) {
            $this->setFolder(Config::FOLDER_ARCHIVE);
        }
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $this->updateFields();

        if ($this->getData('user_id') && ($this->getOrigData('user_id') != $this->getData('user_id'))) {
            $this->helpdeskRuleevent->newEvent(Config::RULE_EVENT_TICKET_ASSIGNED, $this);
        }
        $this->helpdeskRuleevent->newEvent(Config::RULE_EVENT_TICKET_UPDATED, $this);

        return parent::beforeSave();
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        if (
            !$this->getIsSent() && (int)$this->getData('user_id') &&
            ($this->getOrigData('user_id') != $this->getData('user_id')) &&
            $this->getData('message_sender') != $this->getData('user_id')
        ) {
            $this->setIsSent(true);
            $this->helpdeskNotification->notifyUserAssign($this);
        }

        return parent::afterSave();
    }

    /**
     * Overridden superclass function. Deletes all emails linked with current ticket
     *
     * @return $this
     */
    public function beforeDelete()
    {
        $messages = $this->messageCollectionFactory->create()
            ->addFieldToFilter('ticket_id', $this->getId());
        foreach ($messages as $message) {
            $message->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        $return = parent::afterDelete();

        $this->ticketManagement->logTicketDeletion($this);

        return $return;
    }

    /**
     * @param bool|true $useTicketsStore
     * @return string
     */
    public function getUrl($useTicketsStore = true)
    {
        $urlManager = $this->urlManager;
        if ($useTicketsStore && $this->getStoreId()) {
            $urlManager->setScope($this->getStoreId());
        }
        $url = $urlManager->getUrl(
            'helpdesk/ticket/view',
            ['id' => $this->getId(), '_nosid' => true]
        );

        return $url;
    }

    /**
     * @param bool|true $useTicketsStore
     * @return string
     */
    public function getExternalUrl($useTicketsStore = true)
    {
        if (!$this->config->getGeneralIsAllowExternalURLs()) {
            return $this->getUrl($useTicketsStore);
        }
        /*
         * removed '_store' => $this->getStoreId() from url.
         * if necessary, it's better to create redirect (or use some other way)
         */
        $urlManager = $this->urlManager;
        if ($useTicketsStore && $this->getStoreId()) {
            $urlManager->setScope($this->getStoreId());
        }
        $url = $urlManager->getUrl(
            'helpdesk/ticket/external',
            [
                'id' => $this->getExternalId(),
                '_nosid' => true,
                \Magento\Backend\Model\UrlInterface::SECRET_KEY_PARAM_NAME => ''
            ]
        );

        return $url;
    }

    /**
     * @return string
     */
    public function getStopRemindUrl()
    {
        $url = $this->urlManager->getUrl(
            'helpdesk/ticket/stopremind',
            ['id' => $this->getExternalId(), '_nosid' => true]
        );

        return $url;
    }

    /**
     * @return string
     */
    public function getBackendUrl()
    {
        $url = $this->backendUrlManager->getUrl(
            'helpdesk/ticket/edit',
            ['id' => $this->getId(), '_nosid' => true]
        );

        return $url;
    }

    /**
     * @param bool $includePrivate
     *
     * @return ResourceModel\Message\Collection | \Mirasvit\Helpdesk\Model\Message[]
     */
    public function getMessages($includePrivate = false)
    {
        $collection = $this->messageCollectionFactory->create();
        $collection
            ->addFieldToFilter('ticket_id', $this->getId())
            ->setOrder('created_at', 'desc');
        if (!$includePrivate) {
            $collection->addFieldToFilter(
                'type',
                [
                    ['eq' => ''],
                    ['eq' => Config::MESSAGE_PUBLIC],
                    ['eq' => Config::MESSAGE_PUBLIC_THIRD],
                ]
            );
        }

        return $collection;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Message
     */
    public function getLastMessage()
    {
        $collection = $this->messageCollectionFactory->create();
        $collection
            ->addFieldToFilter('ticket_id', $this->getId())
            ->setOrder('message_id', 'asc');

        return $collection->getLastItem();
    }

    /**
     * @return string
     */
    public function getLastMessageHtmlText()
    {
        return $this->getLastMessage()->getUnsafeBodyHtml();
    }

    /**
     * @return string
     */
    public function getLastMessagePlainText()
    {
        return $this->getLastMessage()->getBodyPlain();
    }

    /**
     * @param int $format
     * @return string
     */
    public function getCreatedAtFormated($format = \IntlDateFormatter::LONG)
    {
        $date = new \DateTime($this->getCreatedAt());

        return $this->localeDate->formatDateTime($date, $format);
    }

    /**
     * @param int $format
     * @return string
     */
    public function getUpdatedAtFormated($format = \IntlDateFormatter::LONG)
    {
        $date = new \DateTime($this->getUpdatedAt());

        return $this->localeDate->formatDateTime($date, $format);
    }

    /**
     *
     */
    public function open()
    {
        $status = $this->statusFactory->create()->loadByCode(Config::STATUS_OPEN);
        $this->setStatusId($status->getId())->save();
    }

    /**
     *
     */
    public function close()
    {
        $status = $this->statusFactory->create()->loadByCode(Config::STATUS_CLOSED);
        $this->setStatusId($status->getId())->save();
    }

    /**
     * @return bool
     */
    public function isClosed()
    {
        $status = $this->statusFactory->create()->loadByCode(Config::STATUS_CLOSED);
        if ($status->getId() == $this->getStatusId()) {
            return true;
        }

        return false;
    }

    /**
     * @param string       $value
     * @param string|false $prefix
     * @return $this
     */
    public function initOwner($value, $prefix = false)
    {
        //set ticket user and department
        if ($value) {
            $owner = $value;
            $owner = explode('_', $owner);
            if ($prefix) {
                $prefix .= '_';
            }
            $this->setData($prefix . 'department_id', (int)$owner[0]);
            $this->setData($prefix . 'user_id', (int)$owner[1]);
        }

        return $this;
    }

    /**
     *
     */
    public function markAsSpam()
    {
        $this->setFolder(Config::FOLDER_SPAM)->save();
    }

    /**
     *
     */
    public function markAsNotSpam()
    {
        $this->setFolder(Config::FOLDER_INBOX)->save();
        if ($emailId = $this->getEmailId()) {
            $email = $this->emailFactory->create()->load($emailId);
            $email->setPatternId(0)->save();
        }
    }

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer = null;

    /**
     * @return bool|\Magento\Customer\Model\Customer|\Magento\Framework\DataObject
     */
    public function getCustomer()
    {
        if ($this->customer === null) {
            if ($this->getCustomerId()) {
                $this->customer = $this->customerFactory->create()->load($this->getCustomerId());
            } elseif ($this->getCustomerEmail()) {
                $this->customer = new \Magento\Framework\DataObject([
                    'name'  => $this->getCustomerName(),
                    'email' => $this->getCustomerEmail(),
                ]);
            } else {
                $this->customer = false;
            }
        }

        return $this->customer;
    }

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order = null;

    /**
     * @return bool|\Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->getOrderId()) {
            return false;
        }
        if ($this->order === null) {
            $this->order = $this->orderFactory->create()->load($this->getOrderId());
        }

        return $this->order;
    }

    /**
     * @param string $subject
     * @return string
     */
    public function getEmailSubject($subject = '')
    {
        if ($this->getEmailSubjectPrefix()) {
            $subject = $this->getEmailSubjectPrefix() . $subject;
        }

        return $this->helpdeskEmail->getEmailSubject($this, $subject);
    }

    /**
     * @return string
     */
    public function getHiddenCodeHtml()
    {
        if (!$this->config->getNotificationIsShowCode()) {
            return $this->helpdeskEmail->getHiddenCode($this->getCode());
        }
    }

    /**
     * @return string
     */
    public function getHistoryHtml()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        if ($this->getUser()) {
            return $this->getUser()->getName();
        }
    }

    /**
     * @return ResourceModel\Tag\Collection|Tag[]
     */
    public function getTags()
    {
        $tags = [0];
        if (is_array($this->getTagIds())) {
            $tags = array_merge($tags, $this->getTagIds());
        }
        $collection = $this->tagCollectionFactory->create()
            ->addFieldToFilter('tag_id', $tags);

        return $collection;
    }

    /**
     *
     */
    public function loadTagIds()
    {
        if ($this->getData('tag_ids') === null) {
            $this->getResource()->loadTagIds($this);
        }
    }

    /**
     * @return bool
     */
    public function hasCustomer()
    {
        return $this->getCustomerId() > 0 || $this->getQuoteAddressId() > 0;
    }

    /**
     * @param int $orderId
     *
     * @return $this
     */
    public function initFromOrder($orderId)
    {
        $this->setOrderId($orderId);
        $order = $this->getOrder();
        $address = ($order->getShippingAddress()) ? $order->getShippingAddress() : $order->getBillingAddress();

        $this->setQuoteAddressId($address->getId());
        $this->setCustomerId($order->getCustomerId());
        $this->setStoreId($order->getStoreId());

        if ($this->getCustomerId()) {
            $this->setCustomerEmail($this->getCustomer()->getEmail());
        } elseif ($order->getCustomerEmail()) {
            $this->setCustomerEmail($order->getCustomerEmail());
        } else {
            $this->setCustomerEmail($address->getEmail());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isThirdPartyPublic()
    {
        foreach ($this->getMessages(true) as $message) {
            if ($message->getType() == Config::MESSAGE_PUBLIC_THIRD) {
                return true;
            }
            if ($message->getType() == Config::MESSAGE_INTERNAL_THIRD) {
                return false;
            }
        }

        return true;
    }

    /************************/

    /**
     * Returns owner id. E.g. "1_0" or "2_3".
     *
     * @return string
     */
    public function getOwner()
    {
        return (int)$this->getDepartmentId() . '_' . (int)$this->getUserId();
    }

    /**
     * Adds a text to search index (without ticket saving).
     *
     * @param string $text
     *
     * @return void
     */
    public function addToSearchIndex($text)
    {
        $index = $this->getSearchIndex();
        $newWords = explode(' ', $text);
        $oldWords = explode(' ', $index);
        $words = array_unique(array_merge($newWords, $oldWords));
        $this->setSearchIndex(implode(' ', $words));
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getState()
    {
        return new \Magento\Framework\DataObject($this->getData());
    }
}
