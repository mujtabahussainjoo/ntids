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
 * Class Notification.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Notification extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory
     */
    protected $ticketCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\DepartmentFactory
     */
    protected $departmentFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Email\Model\TemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Email
     */
    protected $helpdeskEmail;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    protected $design;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\User\Model\UserFactory $userFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\CollectionFactory $ticketCollectionFactory,
        \Mirasvit\Helpdesk\Model\DepartmentFactory $departmentFactory,
        \Mirasvit\Helpdesk\Model\FieldFactory $fieldFactory,
        \Mirasvit\Core\Model\Mail\Template\TransportBuilder $transportBuilder,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Mirasvit\Helpdesk\Helper\Email $helpdeskEmail,
        \Mirasvit\Helpdesk\Helper\Field $fieldHelper,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->userFactory = $userFactory;
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->departmentFactory = $departmentFactory;
        $this->fieldFactory = $fieldFactory;
        $this->transportBuilder = $transportBuilder;
        $this->config = $config;
        $this->inlineTranslation = $inlineTranslation;
        $this->helpdeskEmail = $helpdeskEmail;
        $this->fieldHelper = $fieldHelper;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->filesystem = $filesystem;
        $this->design = $design;
        $this->auth = $auth;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $context->getScopeConfig();

        parent::__construct($context);
    }

    //http://www.iana.org/assignments/auto-submitted-keywords/auto-submitted-keywords.xhtml
    const FLAG_AUTO_REPLIED = 'auto-replied';

    const XML_PATH_DESIGN_EMAIL_LOGO = 'design/email/logo';
    const XML_PATH_DESIGN_EMAIL_LOGO_ALT = 'design/email/logo_alt';

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return 'general';
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket  $ticket
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\User\Model\User         $user
     * @return void
     */
    protected function notifyUser($ticket, $customer, $user)
    {
        $ticket->setMessageType(Config::USER);
        $storeId = $ticket->getStoreId();
        if ($ticket->getUserId()) {
            $user = $this->userFactory->create();
            $user->load($ticket->getUserId());
            $this->mail(
                $ticket,
                $customer,
                $user,
                $user->getEmail(),
                $user->getName(),
                $this->getConfig()->getNotificationStaffNewMessageTemplate($storeId),
                $ticket->getLastMessage()->getAttachments()
            );
        } elseif ($department = $ticket->getDepartment()) {
            if ($department->getNotificationEmail()) {
                $this->mail(
                    $ticket,
                    $customer,
                    $user,
                    $department->getNotificationEmail(),
                    $department->getName(),
                    $this->getConfig()->getNotificationStaffNewMessageTemplate($storeId),
                    $ticket->getLastMessage()->getAttachments()
                );
            }
            if ($department->getIsMembersNotificationEnabled()) {
                foreach ($department->getUsers() as $member) {
                    $this->mail(
                        $ticket,
                        $customer,
                        $user,
                        $member->getEmail(),
                        $department->getName(),
                        $this->getConfig()->getNotificationStaffNewMessageTemplate($storeId),
                        $ticket->getLastMessage()->getAttachments()
                    );
                }
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket  $ticket
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\User\Model\User         $user
     * @return void
     */
    protected function notifyCustomer($ticket, $customer, $user)
    {
        $storeId = $ticket->getStoreId();
        $this->mail(
            $ticket,
            $customer,
            $user,
            $ticket->getCustomerEmail(),
            $ticket->getCustomerName(),
            $this->getConfig()->getNotificationNewMessageTemplate($storeId),
            $ticket->getLastMessage()->getAttachments()
        );
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket  $ticket
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\User\Model\User         $user
     * @return void
     */
    protected function notifyThird($ticket, $customer, $user)
    {
        $storeId = $ticket->getStoreId();
        $this->mail(
            $ticket,
            $customer,
            $user,
            $ticket->getThirdPartyEmail(),
            '',
            $this->getConfig()->getNotificationThirdNewMessageTemplate($storeId),
            $ticket->getLastMessage()->getAttachments()
        );
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return void
     */
    public function notifyUserAssign($ticket)
    {
        $ticket->setMessageType(Config::USER);
        $storeId = $ticket->getStoreId();
        if ($ticket->getUserId()) {
            $user = $this->userFactory->create();
            $user->load($ticket->getUserId());
            $this->mail(
                $ticket,
                $ticket->getCustomer(),
                $user,
                $user->getEmail(),
                $user->getName(),
                $this->getConfig()->getNotificationStaffAssignTicketTemplate($storeId),
                $ticket->getLastMessage()->getAttachments()
            );
        }
    }

    /**
     * Send email notification about creation of new ticket.
     *
     * @param \Mirasvit\Helpdesk\Model\Ticket  $ticket
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\User\Model\User         $user
     * @param string                           $triggeredBy
     * @param string                           $messageType
     * @return void
     */
    public function newTicket($ticket, $customer, $user, $triggeredBy, $messageType)
    {
        $storeId = $ticket->getStoreId();
        if ($triggeredBy == Config::CUSTOMER) {
            $this->mail(
                $ticket,
                $customer,
                $user,
                $ticket->getCustomerEmail(),
                $ticket->getCustomerName(),
                $this->getConfig()->getNotificationNewTicketTemplate($storeId),
                [],
                [],
                self::FLAG_AUTO_REPLIED
            );

            if ($department = $ticket->getDepartment()) {
                if ($department->getNotificationEmail()) {
                    $this->mail(
                        $ticket,
                        $customer,
                        $user,
                        $department->getNotificationEmail(),
                        $department->getName(),
                        $this->getConfig()->getNotificationStaffNewTicketTemplate($storeId),
                        $ticket->getLastMessage()->getAttachments()
                    );
                }
                if ($department->getIsMembersNotificationEnabled()) {
                    foreach ($department->getUsers() as $member) {
                        $this->mail(
                            $ticket,
                            $customer,
                            $member,
                            $member->getEmail(),
                            $department->getName(),
                            $this->getConfig()->getNotificationStaffNewTicketTemplate($storeId),
                            $ticket->getLastMessage()->getAttachments()
                        );
                    }
                }
            }
        } else {
            $this->newMessage($ticket, $customer, $user, $triggeredBy, $messageType);
        }

        $this->getRuleEvent()->newEvent(Config::RULE_EVENT_NEW_TICKET, $ticket);
    }

    /**
     * Send email notification about new message in the ticket.
     *
     * @param \Mirasvit\Helpdesk\Model\Ticket  $ticket
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\User\Model\User         $user
     * @param string                           $triggeredBy
     * @param string                           $messageType
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function newMessage($ticket, $customer, $user, $triggeredBy, $messageType)
    {
        if ($messageType == Config::MESSAGE_PUBLIC) {
            if ($triggeredBy == Config::CUSTOMER) {
                $this->notifyUser($ticket, $customer, $user, $triggeredBy);
                $this->getRuleEvent()->newEvent(Config::RULE_EVENT_NEW_CUSTOMER_REPLY, $ticket);
            } elseif ($triggeredBy == Config::USER) {
                $this->notifyCustomer($ticket, $customer, $user, $triggeredBy);
                $this->getRuleEvent()->newEvent(Config::RULE_EVENT_NEW_STAFF_REPLY, $ticket);
            }
        } elseif ($messageType == Config::MESSAGE_PUBLIC_THIRD ||
            $messageType == Config::MESSAGE_INTERNAL_THIRD
        ) {
            if ($triggeredBy == Config::THIRD) {
                $this->notifyUser($ticket, $customer, $user, $triggeredBy);
                $this->getRuleEvent()->newEvent(Config::RULE_EVENT_NEW_THIRD_REPLY, $ticket);
            } elseif ($triggeredBy == Config::USER) {
                $this->notifyThird($ticket, $customer, $user, $triggeredBy);
            }
        } elseif ($messageType == Config::MESSAGE_INTERNAL) {
            /** @var \Magento\User\Model\User $currentUser */
            $currentUser = $this->auth->getUser();
            if ($ticket->getUserId() == 0 || (int)$ticket->getUserId() !== (int)$currentUser->getId()) {
                $this->notifyUser($ticket, $customer, $user, $triggeredBy);
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     */
    public function sendNotificationReminder($ticket)
    {
        $templateName = $this->getConfig()->getNotificationReminderTemplate();
        $recipientEmail = $ticket->getFpRemindEmail();
        $recipientName = '';
        $config = $this->config;
        if ($config->getDeveloperIsActive()) {
            if ($sandboxEmail = $config->getDeveloperSandboxEmail()) {
                $recipientEmail = $sandboxEmail;
            }
        }

        $ticket = $this->ticketCollectionFactory->create()
            ->joinFields()
            ->addFieldToFilter('ticket_id', $ticket->getId())
            ->getFirstItem();
        $department = $this->departmentFactory->create()->load($ticket->getDepartmentId());
        $customer = $ticket->getCustomer();
        $user = $this->userFactory->create()->load($ticket->getUserId());
        $variables = [
            'ticket'     => $ticket,
            'customer'   => $customer,
            'user'       => $user,
            'department' => $department,
        ];
        $this->mail(
            $ticket,
            $ticket->getCustomer(),
            $user,
            $recipientEmail,
            $recipientName,
            $templateName,
            [],
            $variables
        );

    }


    /**
     * @param \Mirasvit\Helpdesk\Model\Satisfaction $satisfaction
     *
     * @return bool
     */
    public function sendNotificationStaffNewSatisfaction($satisfaction)
    {
        $templateName = $this->getConfig()->getNotificationStaffNewSatisfactionTemplate();
        if ($templateName == 'none') {
            return false;
        }
        if (!$ticket = $satisfaction->getTicket()) {
            return false;
        };
        if (!$user = $ticket->getUser()) {
            return false;
        }
        $variables = [];
        $variables['satisfaction'] = $satisfaction;
        $variables['ticket'] = $ticket;
        $variables['user'] = $user;

        $variables['rate_image_url'] = $this->assetRepo->getUrl(
            'Mirasvit_Helpdesk::images/smile/' . $satisfaction->getRate() . '.png',
            ['_area' => 'frontend']
        );

        $storeId = $ticket->getStoreId();

        if ($this->getConfig()->getSatisfactionIsSendResultsOwner($storeId)) {
            $recipientEmail = $user->getEmail();
            $recipientName = $user->getFirstname() . ' ' . $user->getLastname();

            $this->mail(
                $ticket,
                $ticket->getCustomer(),
                $user,
                $recipientEmail,
                $recipientName,
                $templateName,
                [],
                $variables
            );
        }
        if ($emails = $this->getConfig()->getSatisfactionResultsEmail($storeId)) {
            foreach ($emails as $recipientEmail) {
                $recipientName = '';
                $this->mail(
                    $ticket,
                    $ticket->getCustomer(),
                    $user,
                    $recipientEmail,
                    $recipientName,
                    $templateName,
                    [],
                    $variables
                );
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket        $ticket
     * @param \Magento\Customer\Model\Customer|false $customer
     * @param \Magento\User\Model\User|false         $user
     * @param string                                 $recipientEmail
     * @param string                                 $recipientName
     * @param string                                 $templateName
     * @param \Mirasvit\Helpdesk\Model\Attachment[]  $attachments
     * @param array                                  $variables
     * @param bool                                   $emailFlag
     *
     * @return bool
     *
     * @throws \Exception
     * @throws \Zend_Mail_Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function mail(
        $ticket,
        $customer,
        $user,
        $recipientEmail,
        $recipientName,
        $templateName,
        $attachments = [],
        $variables = [],
        $emailFlag = false
    ) {
        if ($templateName == 'none') {
            return false;
        }

        $storeId = $ticket->getStoreId();
        $config = $this->config;
        if ($config->getDeveloperIsActive($storeId)) {
            if ($sandboxEmail = $config->getDeveloperSandboxEmail($storeId)) {
                $recipientEmail = $sandboxEmail;
            }
        }
        $department = $ticket->getDepartment();
        $store = $ticket->getStore();

        if (!$customer) {
            $customer = $ticket->getCustomer();
        }
        if (!$user) {
            $user = $ticket->getUser();
        }

        $this->preprareCustomFieldsLabels($ticket);

        $variables = array_merge(
            $variables,
            [
                'ticket'           => $ticket,
                'customer'         => $customer,
                'user'             => $user,
                'department'       => $department,
                'store'            => $store,
                'preheader_text'   => $this->helpdeskEmail->getPreheaderText($ticket->getLastMessagePlainText()),
                'hidden_separator' => $this->helpdeskEmail->getHiddenSeparator(),
                'use_form_key'     => (bool)$this->scopeConfig->getValue('admin/security/use_form_key'),
            ]
        );

        if (isset($variables['email_subject'])) {
            $variables['email_subject'] = $this->processVariable($variables['email_subject'], $variables, $storeId);
        }
        if (isset($variables['email_body'])) {
            $variables['email_body'] = $this->processVariable($variables['email_body'], $variables, $storeId);
        }

        // Proper sender email names and addresses for department notification
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$name = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('helpdesk/general/sender_name',$storeScope);
		$email = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('helpdesk/general/sender_email',$storeScope);
        
		//$senderName = $store->getFrontendName() . ', ' . $department->getName();
        //$senderEmail = $department->getSenderEmail();
		
        $senderName  = $name;
        $senderEmail = $email;

        if (!$senderEmail) {
            return false;
        }
        if (!$recipientEmail) {
            return false;
        }
        $this->inlineTranslation->suspend();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$transportBuilder = $objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');
		
        // $this->transportBuilder
           $transportBuilder ->setTemplateIdentifier($templateName)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId ?: $this->storeManager->getStore()->getId(),
            ])
            ->setTemplateVars($variables);

        foreach ($attachments as $attachment) {
            if ($attachment->getIsAllowed()) {
                $transportBuilder->addAttachment(
                    $attachment->getBody(),
                    $attachment->getType(),
                    \Zend_Mime::DISPOSITION_ATTACHMENT,
                    \Zend_Mime::ENCODING_BASE64,
                    $attachment->getName()
                );
            }
        }

        $bcc = array_unique(array_merge($this->getConfig()->getGeneralBccEmail($storeId), $ticket->getBcc()));
        $transportBuilder
            ->setFrom([
                'name'  => $senderName,
                'email' => $senderEmail,
            ])
            ->addTo($recipientEmail, $recipientName)
            ->addBcc($this->helpdeskEmail->stripGatewayEmails($bcc));
        if ($ticket->getMessageType() != Config::USER) {
            $transportBuilder->addCc($this->helpdeskEmail->stripGatewayEmails($ticket->getCc()));
        }

        /* @var \Magento\Framework\Mail\Transport $transport */		
        $transport = $transportBuilder->getTransport();
		
		 /* @var custom codes */		
		// $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $transportBuilder = $objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');
		// $transports = $transportBuilder->setTemplateIdentifier($templateName)
							// ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,'store' =>$this->storeManager->getStore()->getId(),])
							// ->setTemplateVars($variables)
							// ->setFrom(['name'  => $senderName,'email' => $senderEmail,])
							// ->addTo($recipientEmail, $recipientName);
		// $transports = $transports->getTransport();
		
		 /* @var custom codes */		
		try {
			$transport->sendMessage();
           // $transport->sendMessage();
        } catch (\Exception $e) {
            $this->context->getLogger()->alert($e->getMessage());
        }

        $this->inlineTranslation->resume();


    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     */
    protected function preprareCustomFieldsLabels($ticket)
    {
        $data = $ticket->getData();
        $customFields = [];
        foreach ($data as $k => $v) {
            if (strpos($k, 'f_') === 0) {
                $customFields[] = $k;
            }
        }
        $collection = $this->fieldFactory->create()->getCollection()
            ->addFieldToFilter('type', \Mirasvit\Helpdesk\Model\Field::TYPE_SELECT)
            ->addFieldToFilter('code', ['in' => $customFields]);
        ;
        foreach ($collection as $field) {
            $value = $this->fieldHelper->getValue($ticket, $field);
            $ticket->setData($field->getCode().'_label', $value);
        }
    }

    /**
     * Can parse template and return ready text.
     *
     * @param string $variable  Text with variables like {{var customer.name}}.
     * @param array  $variables Array of variables.
     * @param int    $storeId
     *
     * @return string - ready text
     */
    public function processVariable($variable, $variables, $storeId)
    {
        $template = $this->emailTemplateFactory->create();
        $template->setDesignConfig([
            'area'  => 'frontend',
            'store' => $storeId,
        ]);
        $template->setTemplateText($variable);
        $html = $template->getProcessedTemplate($variables);

        return $html;
    }

    /**
     * @return \Mirasvit\Helpdesk\Helper\Ruleevent
     */
    protected function getRuleEvent()
    {
        return $this->objectManager->get('\Mirasvit\Helpdesk\Helper\Ruleevent');
    }
}
