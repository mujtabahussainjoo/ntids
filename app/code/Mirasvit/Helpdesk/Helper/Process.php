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



namespace Mirasvit\Helpdesk\Helper;

use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * Class Process.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Process
{
    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $orderAddressFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory
     */
    protected $orderAddressCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\CollectionFactory
     */
    protected $patternCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Customer
     */
    protected $helpdeskCustomer;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History
     */
    protected $helpdeskHistory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag
     */
    protected $helpdeskTag;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Draft
     */
    protected $helpdeskDraft;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Encoding
     */
    protected $helpdeskEncoding;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                              $ticketFactory
     * @param \Magento\Sales\Model\Order\AddressFactory                           $orderAddressFactory
     * @param \Mirasvit\Helpdesk\Model\GatewayFactory                             $gatewayFactory
     * @param \Magento\Sales\Model\OrderFactory                                   $orderFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory     $ticketCollectionFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory            $userCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory          $orderCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory  $orderAddressCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\CollectionFactory    $patternCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory    $messageCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                     $config
     * @param \Mirasvit\Helpdesk\Helper\Customer                                  $helpdeskCustomer
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                                    $helpdeskString
     * @param \Mirasvit\Helpdesk\Helper\Field                                     $helpdeskField
     * @param \Mirasvit\Helpdesk\Helper\History                                   $helpdeskHistory
     * @param \Mirasvit\Helpdesk\Helper\Tag                                       $helpdeskTag
     * @param \Mirasvit\Helpdesk\Helper\Draft                                     $helpdeskDraft
     * @param \Mirasvit\Helpdesk\Helper\Encoding                                  $helpdeskEncoding
     * @param \Magento\Store\Model\StoreManagerInterface                          $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface                $localeDate
     * @param \Magento\Customer\Model\Session                                     $customerSession
     * @param \Magento\Backend\Model\Auth                                         $auth
     * @param \Magento\Framework\App\Helper\Context                               $context
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Magento\Sales\Model\Order\AddressFactory $orderAddressFactory,
        \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $orderAddressCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Pattern\CollectionFactory $patternCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Message\CollectionFactory $messageCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Helper\Customer $helpdeskCustomer,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Mirasvit\Helpdesk\Helper\History $helpdeskHistory,
        \Mirasvit\Helpdesk\Helper\Tag $helpdeskTag,
        \Mirasvit\Helpdesk\Helper\Draft $helpdeskDraft,
        \Mirasvit\Helpdesk\Helper\Encoding $helpdeskEncoding,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->orderAddressFactory = $orderAddressFactory;
        $this->gatewayFactory = $gatewayFactory;
        $this->orderFactory = $orderFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderAddressCollectionFactory = $orderAddressCollectionFactory;
        $this->patternCollectionFactory = $patternCollectionFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->config = $config;
        $this->helpdeskCustomer = $helpdeskCustomer;
        $this->helpdeskString = $helpdeskString;
        $this->helpdeskField = $helpdeskField;
        $this->helpdeskHistory = $helpdeskHistory;
        $this->helpdeskTag = $helpdeskTag;
        $this->helpdeskDraft = $helpdeskDraft;
        $this->helpdeskEncoding = $helpdeskEncoding;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->customerSession = $customerSession;
        $this->auth = $auth;
        $this->context = $context;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Creates ticket from frontend.
     *
     * @param array  $post
     * @param string $channel
     *
     * @return \Mirasvit\Helpdesk\Model\Ticket
     */
    public function createFromPost($post, $channel)
    {
        $ticket = $this->ticketFactory->create();
        // если кастомер не был авторизирован, то ищем его
        $customer = $this->helpdeskCustomer->getCustomerByPost($post);

        $ticket->setCustomerId($customer->getId())
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerName($customer->getName())
            ->setQuoteAddressId($customer->getQuoteAddressId())
            ->setCode($this->helpdeskString->generateTicketCode())
            ->setSubject($post['subject']);
            //->setDescription($this->getEnviromentDescription());

        if (isset($post['priority_id'])) {
            $ticket->setPriorityId((int) $post['priority_id']);
        }
        if (isset($post['department_id'])) {
            $ticket->setDepartmentId((int) $post['department_id']);
        } else {
            $ticket->setDepartmentId($this->getConfig()->getContactFormDefaultDepartment());
        }
        if (isset($post['order_id'])) {
            $ticket->setOrderId((int) $post['order_id']);
        }
        $ticket->setStoreId($this->storeManager->getStore()->getId());
        $ticket->setChannel($channel);
        if ($channel == Config::CHANNEL_FEEDBACK_TAB) {
            $url = $this->customerSession->getFeedbackUrl();
            $ticket->setChannelData(['url' => $url]);
        }

        $this->helpdeskField->processPost($post, $ticket);
        $ticket->save();
        $body = $post['message'];
        // $body = $this->helpdeskString->parseBody($post['message'], Config::FORMAT_PLAIN);
        $ticket->addMessage(
            $body,
            $customer,
            false,
            Config::CUSTOMER,
            Config::MESSAGE_PUBLIC,
            false,
            Config::FORMAT_PLAIN
        );
        $this->helpdeskHistory->changeTicket(
            $ticket, $ticket->getState(), $ticket->getState(), Config::CUSTOMER, ['customer' => $customer]
        );

        return $ticket;
    }

    /**
     * @return string
     */
    public function getEnviromentDescription()
    {
        return print_r($_SERVER, true);
    }

    /**
     * @param array                    $data
     * @param \Magento\User\Model\User $user
     *
     * @return $this
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     *
     * @fixme
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createOrUpdateFromBackendPost($data, $user)
    {
        $ticket = $this->ticketFactory->create();
        if (isset($data['ticket_id']) && (int) $data['ticket_id'] > 0) {
            $ticket->load((int) $data['ticket_id']);
        } else {
            unset($data['ticket_id']);
        }
        if (class_exists('\Magento\Framework\Validator\EmailAddress')) { // for m2.2.x
            $validator = new \Magento\Framework\Validator\EmailAddress();
        } else {
            $validator = new \Zend_Validate_EmailAddress();
        }
        if (!$validator->isValid($data['customer_email'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Customer Email'));
        }
        if (!isset($data['customer_id']) || !$data['customer_id']) {
            if (!$ticket->getCustomerName()) {
                $data['customer_name'] = $data['customer_email'];
            }
        }
        if (isset($data['customer_id']) && strpos($data['customer_id'], 'address_') !== false) {
            $data['quote_address_id'] = (int) str_replace('address_', '', $data['customer_id']);
            $data['customer_id'] = null;
            if ($data['quote_address_id']) {
                $quoteAddress = $this->orderAddressFactory->create();
                $quoteAddress->load($data['quote_address_id']);
                $data['customer_name'] = $quoteAddress->getName();
            }
        } else {
            $data['quote_address_id'] = null;
        }
        $stateBefore = $ticket->getState();

        $ticket->addData($data);

        //        if ($data['allowCC'] == 'false') {
        //            $ticket->setCc('');
        //        }
        //
        //        if ($data['allowBCC'] == 'false') {
        //            $ticket->setBcc('');
        //        }

        $this->helpdeskTag->setTags($ticket, $data['tags']);
        //set custom fields
        $this->helpdeskField->processPost($data, $ticket);
        //set ticket user and department
        if (isset($data['owner'])) {
            $ticket->initOwner($data['owner']);
        }
        if (isset($data['fp_owner'])) {
            $ticket->initOwner($data['fp_owner'], 'fp');
        }
        if (isset($data['fp_period_unit']) && $data['fp_period_unit'] == 'custom') {
            $value = $this->localeDate->formatDate($ticket->getData('fp_execute_at'), \IntlDateFormatter::SHORT);
            $ticket->setData('fp_execute_at', $value);
        } elseif (isset($data['fp_period_value']) && $data['fp_period_value']) {
            $ticket->setData('fp_execute_at', $this->createFpDate($data['fp_period_unit'], $data['fp_period_value']));
        }
        if (!$ticket->getId()) {
            $ticket->setChannel(Config::CHANNEL_BACKEND);
        }

        $this->helpdeskHistory->changeTicket(
            $ticket, $stateBefore, $ticket->getState(), Config::USER, ['user' => $user]
        );
        if (trim($data['reply']) || $_FILES['attachment']['name'][0] != '') {
            $ticket->setMessageSender($user->getId());
        }
        $ticket->save();

        $bodyFormat = Config::FORMAT_PLAIN;
        if ($this->getConfig()->getGeneralIsWysiwyg()) {
            $bodyFormat = Config::FORMAT_HTML;
        }
        if ($ticket->getMessageSender()) {
            $ticket->addMessage($data['reply'], false, $user, Config::USER, $data['reply_type'], false, $bodyFormat);
        }

        $this->helpdeskDraft->clearDraft($ticket);

        return $ticket;
    }

    /**
     * @param string $unit
     * @param int    $value
     *
     * @return string
     */
    public function createFpDate($unit, $value)
    {
        $timeshift = 0;
        switch ($unit) {
            case 'minutes':
                $timeshift = $value;
                break;
            case 'hours':
                $timeshift = $value * 60;
                break;
            case 'days':
                $timeshift = $value * 60 * 24;
                break;
            case 'weeks':
                $timeshift = $value * 60 * 24 * 7;
                break;
            case 'months':
                $timeshift = $value * 60 * 24 * 31;
                break;
        }
        $timeshift *= 60; //in seconds
        $time = strtotime((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
            + $timeshift;
        $time = date('Y-m-d H:i:s', $time);

        return $time;
    }

    /**
     * @return bool
     */
    public function isDev()
    {
        return $this->config->getDeveloperIsActive();
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     * @param string                         $code
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Ticket
     *
     * @throws \Exception
     *
     * @fixme
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function processEmail($email, $code)
    {
        $ticket = false;
        $customer = false;
        $user = false;
        $triggeredBy = Config::CUSTOMER;
        $messageType = Config::MESSAGE_PUBLIC;

        if ($code) {
            //try to find customer for this email
            $tickets = $this->ticketCollectionFactory->create();
            $tickets->addFieldToFilter('code', $code)
                    ->addFieldToFilter('customer_email', $email->getFromEmail())
                    ;
            if ($tickets->count()) {
                $ticket = $tickets->getFirstItem();
            } else {
                //try to find staff user for this email
                $users = $this->userCollectionFactory->create()
                    ->addFieldToFilter('email', $email->getFromEmail())
                    ;

                if ($users->count()) {
                    $user = $users->getFirstItem();
                    $tickets = $this->ticketCollectionFactory->create()
                                ->addFieldToFilter('code', $code)
                                ;
                    if ($tickets->count()) {
                        $ticket = $tickets->getFirstItem();
                        $ticket->setUserId($user->getId());
                        $ticket->save();
                        $triggeredBy = Config::USER;
                    } else {
                        $user = false; //@temp dva for testing
                    }
                } else { //third party
                    $tickets = $this->ticketCollectionFactory->create()
                                ->addFieldToFilter('code', $code)
                                ;
                    if ($tickets->count()) {
                        $ticket = $tickets->getFirstItem();
                        $triggeredBy = Config::THIRD;
                        if ($ticket->isThirdPartyPublic()) {
                            $messageType = Config::MESSAGE_PUBLIC_THIRD;
                        } else {
                            $messageType = Config::MESSAGE_INTERNAL_THIRD;
                        }
                    }
                }
            }
        }

        if (!$user) {
            $customer = $this->helpdeskCustomer->getCustomerByEmail($email);
        }
        // create a new ticket
        if (!$ticket) {
            $ticket = $this->ticketFactory->create();
            if (!$code) {
                $ticket->setCode($this->helpdeskString->generateTicketCode());
            } else {
                $ticket->setCode($code);//temporary for testing to fix @dva
            }
            $gateway = $this->gatewayFactory->create()->load($email->getGatewayId());
            if ($gateway->getId()) {
                if ($gateway->getDepartmentId()) {
                    $ticket->setDepartmentId($gateway->getDepartmentId());
                } else { //if department was removed
                    $departments = $this->departmentCollectionFactory->create()
                                        ->addFieldToFilter('is_active', true);
                    if ($departments->count()) {
                        $department = $departments->getFirstItem();
                        $ticket->setDepartmentId($department->getId());
                    } else {
                        $this->context->getLogger()->error(
                            'Helpdesk MX - Can\'t find any active department. Helpdesk can\'t fetch tickets correctly!'
                        );
                    }
                }
                $ticket->setStoreId($gateway->getStoreId());
            }
            $ticket
                ->setSubject($email->getSubject())
                ->setCustomerName($customer->getName())
                ->setCustomerId($customer->getId())
                ->setQuoteAddressId($customer->getQuoteAddressId())
                ->setCustomerEmail($email->getFromEmail())
                ->setChannel(Config::CHANNEL_EMAIL)
                ->setCc($email->getCc())
                ;

            $ticket->setEmailId($email->getId());
            $ticket->save();
            $pattern = $this->checkForSpamPattern($email);
            if ($pattern) {
                $ticket->markAsSpam();
                if ($email) {
                    $email->setPatternId($pattern->getId())->save();
                }
            }
        }
        $stateBefore = $ticket->getState();

        if ($customer) {
            //parse order ID from email subject
            preg_match_all('[[0-9]{9}]', $email->getSubject(), $numbers);
            foreach ($numbers[0] as $number) {
                $orders = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('increment_id', $number)
                    ->addFieldToFilter('customer_id', $customer->getId());

                if (count($orders)) {
                    // Case 1: this is registered customer and has an order
                    $order = $this->orderFactory->create()->loadByAttribute('increment_id', $number);
                    $ticket->setCustomerId($order->getCustomerId());
                    $ticket->setOrderId($order->getId());
                    $ticket->save();
                    break;
                } else {
                    $order = $this->orderFactory->create()->loadByAttribute('increment_id', $number);
                    $ticket->setOrderId($order->getId());

                    // Case 2: this is known guest customer or known another email of registered customer
                    $prevTickets = $this->ticketCollectionFactory->create()
                        ->addFieldToFilter('customer_email', $email->getFromEmail())
                        ->addFieldToFilter('order_id', $order->getId());
                    if (count($prevTickets)) {
                        $ticket->setCustomerId($order->getCustomerId());
                        $ticket->save();
                        break;
                    }

                    // Case 3: this is generic guest customer with existing order
                    $quotes = $this->orderAddressCollectionFactory->create();
                    $quotes
                        ->addFieldToFilter('email', $email->getFromEmail());
                    $quotes->getSelect()->group('email');
                    if ($quotes->count()) {
                        $ticket->setQuoteAddressId($quotes->getFirstItem()->getId());
                        $ticket->save();
                        break;
                    }
                }
            }
        }

        //add message to ticket
        $text = $email->getBody();
        $encodingHelper = $this->helpdeskEncoding;
        $text = $encodingHelper->toUTF8($text);
        $body = $this->helpdeskString->parseBody($text, $email->getFormat());
        $ticket->addMessage($body, $customer, $user, $triggeredBy, $messageType, $email);

        $this->context->getEventManager()->dispatch(
            'helpdesk_process_email',
            ['body' => $body, 'customer' => $customer, 'user' => $user, 'ticket' => $ticket]
        );

        $this->helpdeskHistory->changeTicket(
            $ticket, $stateBefore, $ticket->getState(), $triggeredBy, [
                'user'     => $user,
                'customer' => $customer,
                'email'    => $email
            ]
        );

        return $ticket;
    }

    /**
     * @param string $email
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Pattern
     */
    public function checkForSpamPattern($email)
    {
        $patterns = $this->patternCollectionFactory->create()
            ->addFieldToFilter('is_active', true);
        foreach ($patterns as $pattern) {
            if ($pattern->checkEmail($email)) {
                return $pattern;
            }
        }

        return false;
    }

    /**
     * Merge selected tickets.
     *
     * @param array $ids Array of ticket identifiers.
     * @return void
     */
    public function mergeTickets($ids)
    {
        // Sort ids in ascending order
        sort($ids);

        $baseTicket = $this->ticketFactory->create()->load($ids[0]);

        // Get all messages, registered in selected tickets and merge it to oldest
        $mergeMessages = $this->messageCollectionFactory->create()
                                ->addFieldToFilter('ticket_id', $ids);
        foreach ($mergeMessages as $msg) {
            $msg->setTicketId($baseTicket->getId());
            $msg->save();
        }

        // Add to merged tickets new message instead of moved ones
        $mergeMessage = __('Ticket was merged to %1', $baseTicket->getCode().' - ');
        /** @var \Magento\User\Model\User $user */
        $user = $this->auth->getUser();
        $mergeCodes = [];
        foreach ($ids as $id) {
            if ($id == $baseTicket->getId()) {
                continue;
            }

            $ticket = $this->ticketFactory->create()->load($id);
            $ticket
                ->setMergedTicketId($baseTicket->getId())
                ->setFolder(Config::FOLDER_ARCHIVE)
                ->addMessage(
                    $mergeMessage.'[[ticket_url__'.$baseTicket->getId().']]',
                    null,
                    $user,
                    Config::USER,
                    Config::MESSAGE_INTERNAL
                )
                ->save();
            $ticket->close();
            $mergeCodes[] = $ticket->getCode();
            $this->helpdeskHistory->changeTicket(
                $ticket, $ticket->getState(), $ticket->getState(), Config::USER, ['user' => $user]
            );
        }
        $this->helpdeskHistory->changeTicket(
            $baseTicket,
            $baseTicket->getState(),
            $baseTicket->getState(),
            Config::USER,
            ['user' => $user, 'codes' => $mergeCodes]
        );
    }
}
