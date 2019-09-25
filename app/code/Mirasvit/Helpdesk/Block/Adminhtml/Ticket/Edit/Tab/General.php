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

use Mirasvit\Helpdesk\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class General extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\StatusFactory $statusFactory,
        \Mirasvit\Helpdesk\Model\PriorityFactory $priorityFactory,
        //        \Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory $rmaCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\Config\Wysiwyg $configWysiwyg,
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Mirasvit\Helpdesk\Helper\Mage $helpdeskMage,
        \Mirasvit\Helpdesk\Helper\Draft $helpdeskDraft,
        \Mirasvit\Helpdesk\Helper\Customer $helpdeskCustomer,
        \Mirasvit\Helpdesk\Helper\Order $helpdeskOrder,
        \Mirasvit\Helpdesk\Helper\Html $helpdeskHtml,
        \Magento\Framework\Data\Form\Element\Factory $formElementFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonEncoder,
        array $data = []
    ) {
        $this->statusFactory             = $statusFactory;
        $this->priorityFactory           = $priorityFactory;
        //        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->config                    = $config;
        $this->configWysiwyg             = $configWysiwyg;
        $this->helpdeskField             = $helpdeskField;
        $this->helpdeskMage              = $helpdeskMage;
        $this->helpdeskDraft             = $helpdeskDraft;
        $this->helpdeskCustomer          = $helpdeskCustomer;
        $this->helpdeskOrder             = $helpdeskOrder;
        $this->helpdeskHtml              = $helpdeskHtml;
        $this->formElementFactory        = $formElementFactory;
        $this->wysiwygConfig             = $wysiwygConfig;
        $this->formFactory               = $formFactory;
        $this->backendUrlManager         = $backendUrlManager;
        $this->moduleManager             = $moduleManager;
        $this->registry                  = $registry;
        $this->auth                      = $auth;
        $this->context                   = $context;
        $this->jsonEncoder               = $jsonEncoder;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $messages = $this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Messages');
        $this->setChild('helpdesk_messages', $messages);
        $this->setTemplate('ticket/edit/tab/general.phtml');

        return parent::_prepareLayout();
    }

    /**
     * @return object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerSummaryHtml()
    {
        return $this->getLayout()->createBlock(
            'Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\General\CustomerSummary'
        )
            ->setTemplate('Mirasvit_Helpdesk::ticket/edit/tab/general/customer_summary.phtml')
            ->toHtml();
    }

    /**
     * @return \Magento\Framework\Data\Form\Element\Collection|\Magento\Framework\Data\Form\Element\AbstractElement[]
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomFields()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        $ticket = $this->getTicket();

        $collection = $this->helpdeskField->getStaffCollection();
        if ($ticket->getStoreId()) {
            $collection->addStoreFilter($ticket->getStoreId());
        }
        if (!$collection->count()) {
            return [];
        }
        foreach ($collection as $field) {
            if ($field->getType() == 'checkbox') {
                $form->addField($field->getCode().'1', 'hidden', ['name' => $field->getCode(), 'value' => 0]);
            }
            $params = $this->helpdeskField->getInputParams($field, true, $ticket);
            $form->addField(
                $field->getCode(),
                $field->getType(),
                $params
            );
        }
        return $form->getElements();
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     *
     */
    public function getMessagesHtml()
    {
        return $this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Edit\Tab\Messages')->toHtml();
    }

    /**
     * @return object
     */
    public function getTicket()
    {
        return $this->registry->registry('current_ticket');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getNoticeMessage()
    {
        $ticket = $this->getTicket();
        if (!$ticket->getId()) {
            return '';
        }
        $userId = $this->auth->getUser()->getUserId();

        return $this->helpdeskDraft->getNoticeMessage($ticket->getId(), $userId);
    }

    /**
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlManager()
    {
        return $this->context->getUrlBuilder();
    }

    /**
     * @return string
     */
    public function getCustomerSummaryConfigJson()
    {
        $ticket = $this->getTicket();
        $customersOptions = [];
        $ordersOptions = [['name' => __('Unassigned'), 'id' => 0]];
        if ($ticket->getCustomerId() || $ticket->getQuoteAddressId()) {
            $customers = $this->helpdeskCustomer->getCustomerArray(
                false,
                $ticket->getCustomerId(),
                $ticket->getQuoteAddressId()
            );
            $email = false;
            foreach ($customers as $value) {
                $customersOptions[] = ['name' => $value['name'], 'id' => $value['id']];
                $email = $value['email'];
            }

            $orders = $this->helpdeskOrder->getOrderArray($email, $ticket->getCustomerId());
            foreach ($orders as $value) {
                $ordersOptions[] = ['name' => $value['name'], 'id' => $value['id'], 'url' => $value['url']];
            }
        }

        $config = [
            '_customer' => [
                'id'     => $ticket->getCustomerId(),
                'email'  => $ticket->getCustomerEmail(),
                'cc'     => $ticket->getCc() ? implode(', ', $ticket->getCc()) : '',
                'bcc'    => $ticket->getBcc() ? implode(', ', $ticket->getBcc()) : '',
                'name'   => $ticket->getCustomerName(),
                'orders' => $ordersOptions,
            ],
            '_orderId' => $ticket->getOrderId(),
            '_emailTo' => $ticket->getCustomerEmail(),
            '_autocompleteUrl' => $this->getUrlManager()->getUrl('helpdesk/ticket/customerfind'),
        ];

        return $this->jsonEncoder->jsonEncode($config);
    }

    /**
     * @return string
     */
    public function getEditField()
    {
        $form = $this->formFactory->create();
        $fieldset = $this->formElementFactory->create('fieldset', ['data' => ['legend' => __('General Information')]]);
        $fieldset->setForm($form);
        $fieldset->setId('edit_fieldset');
        $fieldset->setAdvanced(false);
        if ($this->config->getGeneralIsWysiwyg()) {
            $fieldset->addField('reply', 'editor', [
                'label'   => '',
                'name'    => 'reply',
                'class'   => 'hdmx__reply-area',
                'wysiwyg' => true,
                'config'  => $this->wysiwygConfig->getConfig(),
                'value'   => $this->getDraftText(),
            ]);
        } else {
            $fieldset->addField('reply', 'textarea', [
                'label' => __('Template'),
                'name'  => 'reply',
                'class' => 'hdmx__reply-area',
                'value' => $this->getDraftText(),
            ]);
        }

        return $this->jsonEncoder->jsonEncode($fieldset->getChildrenHtml());
    }

    /**
     * @return string
     */
    public function getReplySwitcherJson()
    {
        $ticket = $this->getTicket();
        $config = [
            '_thirdPartyEmail' => $ticket->getThirdPartyEmail(),
        ];

        return $this->jsonEncoder->jsonEncode($config);
    }

    /**
     * @return string
     */
    public function getQuickRespoinsesJson()
    {
        $ticket = $this->getTicket();
        $collection = $this->templateCollectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->setOrder('name', 'asc');
        if ($ticket->getId()) {
            $collection->addStoreFilter($ticket->getStoreId());
        }
        $templates = [];
        if ($collection->count()) {
            foreach ($collection as $template) {
                $templates[] = [
                    'id' => $template->getId(),
                    'name' => $template->getName(),
                    'body' => trim($template->getParsedTemplate($ticket)),
                ];
            }
        }

        $config = [
            '_templates' => $templates,
        ];

        return $this->jsonEncoder->jsonEncode($config);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Status\Collection|\Mirasvit\Helpdesk\Model\Status[]
     */
    public function getStatusCollection()
    {
        $ticket = $this->getTicket();

        return $this->statusFactory->create()->getPreparedCollection($ticket->getStoreId());
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Priority[]|\Mirasvit\Helpdesk\Model\ResourceModel\Priority\Collection
     */
    public function getPriorityCollection()
    {
        $ticket = $this->getTicket();

        return $this->priorityFactory->create()->getPreparedCollection($ticket->getStoreId());
    }

    /**
     * @return array
     */
    public function getAdminOwnerOptionArray()
    {
        $ticket = $this->getTicket();

        return $this->helpdeskHtml->getAdminOwnerOptionArray(false, $ticket->getStoreId());
    }

    /**
     * @return bool
     */
    public function isAllowDraft()
    {
        return $this->getConfig()->getDesktopIsAllowDraft();
    }

    /**
     * @return int
     */
    public function getDraftInterval()
    {
        return $this->getConfig()->getDesktopDraftUpdatePeriod();
    }

    /**
     * @return string
     */
    public function getDrafUpdateUrl()
    {
        return $this->getUrl('helpdesk/draft/update');
    }

    /**
     * @return string
     */
    public function getDraftText()
    {
        $text   = '';
        $ticket = $this->getTicket();
        if ($ticket && $ticket->getId() && $draft = $this->helpdeskDraft->getSavedDraft($this->getTicket()->getId())) {
            $text = $draft->getBody();
        }

        return $text;
    }
}
