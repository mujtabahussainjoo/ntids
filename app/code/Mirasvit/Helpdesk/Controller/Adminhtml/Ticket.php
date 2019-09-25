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



namespace Mirasvit\Helpdesk\Controller\Adminhtml;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class Ticket extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\MessageFactory
     */
    protected $messageFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Process
     */
    protected $helpdeskProcess;

    /**
     * @var \Mirasvit\Helpdesk\Helper\History
     */
    protected $helpdeskHistory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission
     */
    protected $helpdeskPermission;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Customer
     */
    protected $helpdeskCustomer;

    /**
     * @var \Mirasvit\Helpdesk\Helper\User
     */
    protected $helpdeskUser;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\DesktopNotification
     */
    protected $desktopNotificationHelper;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Customer\Model\CustomerFactory       $customerFactory
     * @param \Mirasvit\Helpdesk\Model\TicketFactory        $ticketFactory
     * @param \Mirasvit\Helpdesk\Model\StatusFactory        $statusFactory
     * @param \Mirasvit\Helpdesk\Model\AttachmentFactory    $attachmentFactory
     * @param \Mirasvit\Helpdesk\Model\MessageFactory       $messageFactory
     * @param \Mirasvit\Helpdesk\Helper\Process             $helpdeskProcess
     * @param \Mirasvit\Helpdesk\Helper\History             $helpdeskHistory
     * @param \Mirasvit\Helpdesk\Helper\Permission          $helpdeskPermission
     * @param \Mirasvit\Helpdesk\Helper\Customer            $helpdeskCustomer
     * @param \Mirasvit\Helpdesk\Helper\User                $helpdeskUser
     * @param \Mirasvit\Helpdesk\Helper\DesktopNotification $desktopNotificationHelper
     * @param \Mirasvit\Helpdesk\Model\Config               $config
     * @param \Magento\Store\Model\StoreManagerInterface    $storeManager
     * @param \Magento\Framework\Registry                   $registry
     * @param \Magento\Framework\Json\Helper\Data           $jsonEncoder
     * @param \Magento\Framework\Escaper                    $escaper
     * @param \Magento\Backend\App\Action\Context           $context
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Model\StatusFactory $statusFactory,
        \Mirasvit\Helpdesk\Model\AttachmentFactory $attachmentFactory,
        \Mirasvit\Helpdesk\Model\MessageFactory $messageFactory,
        \Mirasvit\Helpdesk\Helper\Process $helpdeskProcess,
        \Mirasvit\Helpdesk\Helper\History $helpdeskHistory,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Mirasvit\Helpdesk\Helper\Customer $helpdeskCustomer,
        \Mirasvit\Helpdesk\Helper\User $helpdeskUser,
        \Mirasvit\Helpdesk\Helper\DesktopNotification $desktopNotificationHelper,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->customerFactory = $customerFactory;
        $this->ticketFactory = $ticketFactory;
        $this->statusFactory = $statusFactory;
        $this->attachmentFactory = $attachmentFactory;
        $this->messageFactory = $messageFactory;
        $this->helpdeskProcess = $helpdeskProcess;
        $this->helpdeskHistory = $helpdeskHistory;
        $this->helpdeskPermission = $helpdeskPermission;
        $this->helpdeskCustomer = $helpdeskCustomer;
        $this->helpdeskUser = $helpdeskUser;
        $this->desktopNotificationHelper = $desktopNotificationHelper;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->escaper = $escaper;
        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('helpdesk');

        return $this;
    }

    /**
     *
     */
    protected function saveStoreSelection()
    {
        $storeId = $this->getRequest()->getParam('store');
        if ($storeId || $storeId == -1) { //small hack here. i don't see other way to solve this.
            if ($storeId == -1) {
                $storeId = null;
            }
            if ($helpdeskUser = $this->helpdeskUser->getHelpdeskUser()) {
                $helpdeskUser->setStoreId($storeId);
                $helpdeskUser->save();
            }
        }
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Init ticket.
     *
     * @return \Mirasvit\Helpdesk\Model\Ticket
     */
    public function _initTicket()
    {
        $ticket = $this->ticketFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $ticket->load($this->getRequest()->getParam('id'));
            $this->helpdeskPermission->checkReadTicketRestrictions($ticket);
        }

        $this->registry->register('current_ticket', $ticket);

        return $ticket;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_ticket');
    }

    /**
     *
     */
    public function execute()
    {
    }

    /************************/
}
