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

use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(ExcessivePublicCount)
 */
class Config
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Config\Model\Config\Backend\Admin\Custom  $configAdminCustom
     * @param \Magento\Framework\Module\Manager                  $moduleManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Context                   $context
     */
    public function __construct(
        \Magento\Config\Model\Config\Backend\Admin\Custom $configAdminCustom,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context
    ) {
        $this->configAdminCustom = $configAdminCustom;
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
    }

    const NOTIFICATION_TYPE_NEW_TICKET = 'new_ticket';
    const NOTIFICATION_TYPE_NEW_MESSAGE = 'new_message';
    const NOTIFICATION_TYPE_NEW_ASSIGN = 'reassign';

    const FOLDER_INBOX = 1;
    const FOLDER_ARCHIVE = 2;
    const FOLDER_SPAM = 3;

    const FOLLOWUPPERIOD_MINUTES = 'minutes';
    const FOLLOWUPPERIOD_HOURS = 'hours';
    const FOLLOWUPPERIOD_DAYS = 'days';
    const FOLLOWUPPERIOD_WEEKS = 'weeks';
    const FOLLOWUPPERIOD_MONTHS = 'months';
    const FOLLOWUPPERIOD_CUSTOM = 'custom';
    const PROTOCOL_POP3 = 'pop3';
    const PROTOCOL_IMAP = 'imap';
    const ENCRYPTION_NONE = 'none';
    const ENCRYPTION_SSL = 'ssl';
    const SCOPE_HEADERS = 'headers';
    const SCOPE_SUBJECT = 'subject';
    const SCOPE_BODY = 'body';
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_TEXTAREA = 'textarea';
    const FIELD_TYPE_DATE = 'date';
    const FIELD_TYPE_CHECKBOX = 'checkbox';
    const FIELD_TYPE_SELECT = 'select';
    const RATE_3 = 3;
    const RATE_2 = 2;
    const RATE_1 = 1;
    const RULE_EVENT_NEW_TICKET              = 'new_ticket';
    const RULE_EVENT_NEW_CUSTOMER_REPLY      = 'new_customer_reply';
    const RULE_EVENT_NEW_STAFF_REPLY         = 'new_staff_reply';
    const RULE_EVENT_NEW_THIRD_REPLY         = 'new_third_reply';
    const RULE_EVENT_TICKET_ASSIGNED         = 'ticket_assigned';
    const RULE_EVENT_TICKET_UPDATED          = 'ticket_updated';
    const RULE_EVENT_TICKET_CONVERTED_TO_RMA = 'ticket_converted_to_rma';
    const RULE_EVENT_CRON_EVERY_HOUR         = 'cron_every_hour';
    const IS_ARCHIVE_TO_ARCHIVE   = 1;
    const IS_ARCHIVE_FROM_ARCHIVE = 2;
    const TICKET_GRID_COLUMNS_CODE            = 'code';
    const TICKET_GRID_COLUMNS_NAME            = 'name';
    const TICKET_GRID_COLUMNS_CUSTOMER_NAME   = 'customer_name';
    const TICKET_GRID_COLUMNS_LAST_REPLY_NAME = 'last_reply_name';
    const TICKET_GRID_COLUMNS_USER_ID         = 'user_id';
    const TICKET_GRID_COLUMNS_DEPARTMENT_ID   = 'department_id';
    const TICKET_GRID_COLUMNS_STORE_ID        = 'store_id';
    const TICKET_GRID_COLUMNS_STATUS_ID       = 'status_id';
    const TICKET_GRID_COLUMNS_PRIORITY_ID     = 'priority_id';
    const TICKET_GRID_COLUMNS_REPLY_CNT       = 'reply_cnt';
    const TICKET_GRID_COLUMNS_CREATED_AT      = 'created_at';
    const TICKET_GRID_COLUMNS_UPDATED_AT      = 'updated_at';
    const TICKET_GRID_COLUMNS_LAST_REPLY_AT   = 'last_reply_at';
    const TICKET_GRID_COLUMNS_LAST_ACTIVITY   = 'last_activity';
    const TICKET_GRID_COLUMNS_ACTION          = 'action';
    const SIGN_TICKET_BY_DEPARTMENT = 'department';
    const SIGN_TICKET_BY_USER       = 'user';
    const ACCEPT_FOREIGN_TICKETS_DISABLE = 'disable';
    const ACCEPT_FOREIGN_TICKETS_AW      = 'aw';
    const ACCEPT_FOREIGN_TICKETS_MW      = 'mw';
    const ATTACHMENT_STORAGE_FS = 'fs';
    const ATTACHMENT_STORAGE_DB = 'db';

    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const FORMAT_PLAIN = 1;
    const FORMAT_HTML = 2;

    const CHANNEL_FEEDBACK_TAB = 'feedback_tab';
    const CHANNEL_CONTACT_FORM = 'contact_form';
    const CHANNEL_CUSTOMER_ACCOUNT = 'customer_account';
    const CHANNEL_EMAIL = 'email';
    const CHANNEL_BACKEND = 'backend';

    const MESSAGE_PUBLIC = 'public';
    const MESSAGE_INTERNAL = 'internal';
    const MESSAGE_PUBLIC_THIRD = 'public_third';
    const MESSAGE_INTERNAL_THIRD = 'internal_third';

    const CUSTOMER = 'customer';
    const USER = 'user';
    const THIRD = 'third';
    const RULE = 'rule';

    const SCHEDULE_TYPE_ALWAYS = 'always';
    const SCHEDULE_TYPE_CUSTOM = 'custom';
    const SCHEDULE_TYPE_CLOSED = 'closed';
    const SCHEDULE_LEFT_HOUR_TO_OPEN_PLACEHOLDER = '[time_left_to_open]';
    const SCHEDULE_STATUS_BLOCK_CACHE_LIFETIME = 30;
    const SCHEDULE_BLOCK_CACHE_LIFETIME = 60;

    const DEFAULT_SORT_ORDER = \Magento\Framework\Data\Collection::SORT_ORDER_ASC;

    /**
     * @param null $store
     * @return object
     */
    public function getDefaultFrontName($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/front_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDefaultPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/default_priority',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDefaultStatus($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/default_status',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralSignTicketBy($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/sign_ticket_by',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array
     */
    public function getGeneralArchivedStatusList($store = null)
    {
        $value = $this->scopeConfig->getValue(
            'helpdesk/general/archived_status_list',
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return explode(',', $value);
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralContactUsIsActive($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/contact_us_is_active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralIsAllowExternalURLs($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/is_allow_external_urls',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }


    /**
     * @param null $store
     * @return array|object
     */
    public function getGeneralBccEmail($store = null)
    {
        $cc = $this->scopeConfig->getValue(
            'helpdesk/general/bcc_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($cc) {
            $cc = explode(',', $cc);
            $cc = array_map('trim', $cc);

            return $cc;
        }

        return [];
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralIsWysiwyg($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/is_wysiwyg',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralIsShowButtons($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/prev_next_buttons',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralIsDefaultCron($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/is_default_cron',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralAcceptForeignTickets($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/accept_foreign_tickets',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getGeneralAttachmentStorage($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/attachment_storage',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array
     */
    public function getGeneralAllowedAttachments($store = null)
    {
        $string = $this->scopeConfig->getValue(
            'helpdesk/general/allowed_attachements',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
        $extensions = [];
        if ($string) {
            $extensions = explode(',', $string);
        }
        $extensions = array_map('trim', $extensions);

        return $extensions;
    }

    /**
     * @param null $store
     * @return array
     */
    public function getGeneralShowInCustomerMenu($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/general/show_in_customer_menu',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return int
     */
    public function getDesktopDraftUpdatePeriod($store = null)
    {
        return (int)$this->scopeConfig->getValue(
            'helpdesk/general/draft_update_period',
            ScopeInterface::SCOPE_STORE,
            $store
        ) * 1000;
    }

    /**
     * @param null $store
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDesktopIsAllowDraft($store = null)
    {
        return (bool)$this->getDesktopDraftUpdatePeriod($store);
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormIsActive($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/is_active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormDefaultDepartment($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/default_department',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getColor($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/color',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getPosition($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/position',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getFormTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/form_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getSubjectTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/subject_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getSubjectPlaceholder($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/subject_placeholder',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDescriptionTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/description_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDescriptionPlaceholder($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/description_placeholder',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormIsActiveAttachment($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/is_active_attachment',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormIsAllowPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/is_allow_priority',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormIsAllowDepartment($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/is_allow_department',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getContactFormIsActiveKb($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/contact_form/is_active_kb',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationIsShowCode($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/is_show_code',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationHistoryRecordsNumber($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/history_records_number',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationNewTicketTemplate($store = null)
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$customerTemplate = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('helpdesk/notification/new_ticket_template',$storeScope);
        //exit($customerTemplate);
		return $customerTemplate;
        //return $this->scopeConfig->getValue('helpdesk/notification/new_ticket_template', ScopeInterface::SCOPE_STORE,$store);
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationStaffNewTicketTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/staff_new_ticket_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationNewMessageTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/new_message_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationStaffNewMessageTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/staff_new_message_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationStaffAssignTicketTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/staff_assign_ticket_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationThirdNewMessageTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/third_new_message_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationReminderTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/reminder_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationRuleTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/rule_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getNotificationStaffNewSatisfactionTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/notification/staff_new_satisfaction_template',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getSatisfactionIsActive($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/satisfaction/is_active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getSatisfactionIsShowResultsInTicket($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/satisfaction/is_show_results_in_ticket',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getSatisfactionIsSendResultsOwner($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/satisfaction/is_send_results_owner',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return array
     */
    public function getSatisfactionResultsEmail($store = null)
    {
        $result = trim($this->scopeConfig->getValue(
            'helpdesk/satisfaction/results_email',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
        if ($result) {
            return explode(',', $result);
        }
    }

    /**
     * @param null $store
     * @return object
     */
    public function getFrontendIsActive($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/frontend/is_active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getFrontendIsAllowPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/frontend/is_allow_priority',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getFrontendIsAllowDepartment($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/frontend/is_allow_department',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getFrontendIsAllowOrder($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/frontend/is_allow_order',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDeveloperIsActive($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/developer/is_active',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDeveloperSandboxEmail($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/developer/sandbox_email',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDeveloperApplyStyles($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/developer/apply_styles',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return object
     */
    public function getDeveloperLogTicketDeletion($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/developer/log_ticket_deletion',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param int $store
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDesktopNotificationIsActive($store = null)
    {
        return (bool) ($this->getDesktopNotificationCheckPeriod($store) > 0);
    }

    /**
     * @param int $store
     * @return int
     */
    public function getDesktopNotificationCheckPeriod($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/desktop_notification/check_period',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param null $store
     * @return int[]
     */
    public function getDesktopNotificationAboutTicketUserIds($store = null)
    {
        return explode(',',
            $this->scopeConfig->getValue(
                'helpdesk/desktop_notification/is_notification_about_ticket_user_ids',
                ScopeInterface::SCOPE_STORE,
                $store
            ));
    }

    /**
     * @param int $store
     * @return bool|int
     */
    public function getDesktopNotificationAboutMessage($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/desktop_notification/is_notification_allow_message',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int $store
     * @return bool|int
     */
    public function getDesktopNotificationAboutAssign($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/desktop_notification/is_notification_allow_assign',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isActiveRma()
    {
        if ($this->moduleManager->isEnabled('Mirasvit_Rma')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Mirasvit\Rma\Service\Config\HelpdeskConfig $rmaConfig */
            $rmaConfig = $objectManager->create('Mirasvit\Rma\Service\Config\HelpdeskConfig');
            if (method_exists($rmaConfig, 'isHelpdeskActive') && $rmaConfig->isHelpdeskActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param null $store
     * @return array|object
     */
    public function getSolvedStatuses($store = null)
    {
        $statues = $this->scopeConfig->getValue(
            'helpdesk/report/solved_status',
            ScopeInterface::SCOPE_STORE,
            $store
        );
        $statues = array_filter(explode(',', $statues));
        $statues[] = 0;

        return $statues;
    }

    /**
     * @param string $store
     * @return bool|int
     */
    public function getScheduleIsShowStatusOnContactUs($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/is_show_status_on_contactus',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return bool|int
     */
    public function getScheduleIsShowStatusOnFeedbackPopup($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/is_show_status_on_feedbackpopup',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return bool|int
     */
    public function getScheduleIsShowStatusOnMyTickets($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/is_show_status_on_mytickets',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getScheduleDefaultOpenMessage($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/default_open_message',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getScheduleDefaultClosedMessage($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/default_close_message',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return bool|int
     */
    public function getScheduleIsShowScheduleOnContactUs($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/is_show_schedule_on_contactus',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return int
     */
    public function getScheduleShowHolidayScheduleBeforeDays($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/show_holiday_schedule_before_days',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getWorkingScheduleTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/working_schedule_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $store
     * @return string
     */
    public function getUpcomingScheduleTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/schedule/upcoming_schedule_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|object $store
     * @return string
     */
    public function getExtendedSettingsHelpText($store = null)
    {
        return $this->scopeConfig->getValue(
            'helpdesk/extended/contactus_help_text',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
