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


namespace Mirasvit\Helpdesk\Block\Adminhtml\Ticket;

use Mirasvit\Helpdesk\Model\Config as Config;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config\Wysiwyg
     */
    protected $configWysiwyg;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission
     */
    protected $helpdeskPermission;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\Config             $config
     * @param \Mirasvit\Helpdesk\Model\Config\Wysiwyg     $configWysiwyg
     * @param \Mirasvit\Helpdesk\Service\Config\RmaConfig $rmaConfig
     * @param \Mirasvit\Helpdesk\Helper\Permission        $helpdeskPermission
     * @param \Magento\Backend\Model\Url                  $backendUrlManager
     * @param \Magento\Framework\Registry                 $registry
     * @param \Magento\Backend\Block\Widget\Context       $context
     * @param array                                       $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Grid\Collection $gridCollection,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\Config\Wysiwyg $configWysiwyg,
        \Mirasvit\Helpdesk\Service\Config\RmaConfig $rmaConfig,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->gridCollection     = $gridCollection;
        $this->config             = $config;
        $this->configWysiwyg      = $configWysiwyg;
        $this->rmaConfig          = $rmaConfig;
        $this->helpdeskPermission = $helpdeskPermission;
        $this->backendUrlManager  = $backendUrlManager;
        $this->registry           = $registry;
        $this->context            = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_ticket';
        $this->_blockGroup = 'Mirasvit_Helpdesk';
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('save');

        if ($this->getTicket() && $this->getTicket()->getId()) {
            $this->addPrevButton();
            $this->addNextButton();
            $this->addRmaButton();
        }

        return $this;
    }

    /**
     * Add Previous button
     *
     * @return void
     */
    public function addPrevButton()
    {
        if (!$this->config->getGeneralIsShowButtons()) {
            return;
        }

        $prevTicket = $this->gridCollection->getPrevTicket($this->getTicket()->getId());

        if ($prevTicket) {
            $this->buttonList->add(
                'prev_ticket',
                [
                    'label' => __('Previous Ticket'),
                    'onclick' => 'setLocation(\'' . $this->getBackendTicketUrl($prevTicket) . '\')',
                ]
            );
        }
    }

    /**
     * Add Next button
     *
     * @return void
     */
    public function addNextButton()
    {
        if (!$this->config->getGeneralIsShowButtons()) {
            return;
        }

        $nextTicket = $this->gridCollection->getNextTicket($this->getTicket()->getId());

        if ($nextTicket) {
            $this->buttonList->add(
                'next_ticket',
                [
                    'label' => __('Next Ticket'),
                    'onclick' => 'setLocation(\'' . $this->getBackendTicketUrl($nextTicket) . '\')',
                ]
            );
        }
    }

    /**
     * Add Convert ot RMA button
     *
     * @return void
     */
    public function addRmaButton()
    {
        if (!$this->rmaConfig->isRmaActive()) {
            return;
        }
        $this->buttonList->add(
            'conver_to_rma',
            [
                'label' => __('Convert to RMA'),
                'onclick' => 'var win=window.open(\''.$this->getRmaUrl().'\', \'_blank\'); win.focus();',
            ]
        );
    }

    /**
     * @param int $ticketId
     * @return string
     */
    protected function getBackendTicketUrl($ticketId)
    {
        return $this->backendUrlManager->getUrl(
            'helpdesk/ticket/edit',
            ['id' => $ticketId, '_nosid' => true]
        );
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        $this->setupButtons();

        return parent::_prepareLayout();
    }

    /**
     *
     */
    protected function setupButtons()
    {
        $ticket = $this->getTicket();
        if ($ticket) {
            $this->addEditButtons();
        } else {
            $this->addNewButtons();
        }
    }

    /**
     *
     */
    protected function addNewButtons()
    {
        $options = [];

        $target = '#edit_form';
        $options[] = [
            'id' => 'close-button',
            'label' => __('Save'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save', 'target' => $target]],
                'save-target' => $target,
            ],
            'default' => true,
        ];

        $options[] = [
            'id' => 'edit-button',
            'label' => __('Save & Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit', 'target' => $target],
                ],
                'save-target' => $target,
            ],
        ];

        $this->getToolbar()->addChild(
            'save-split-button',
            'Magento\Backend\Block\Widget\Button\SplitButton',
            [
                'id' => 'save-split-button',
                'label' => __('Save'),
                'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                'button_class' => 'widget-button-save',
                'options' => $options,
            ]
        );
    }

    /**
     *
     */
    protected function addEditButtons()
    {
        $ticket = $this->getTicket();

        $options = [];

        $target = '#edit_form';
        $options[] = [
            'id' => 'close-button',
            'label' => __('Save'),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save', 'target' => $target]],
                'save-target' => $target,
            ],
            'default' => true,
        ];

        $options[] = [
            'id' => 'edit-button',
            'label' => __('Save & Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit', 'target' => $target],
                ],
                'save-target' => $target,
            ],
        ];


        if ($ticket->getFolder() != Config::FOLDER_ARCHIVE) {
            $options[] = [
                'id' => 'archive-button',
                'label' => __('To Archive'),
                'onclick' => "setLocation('".$this->getArchiveUrl()."')",
            ];
        }
        if ($ticket->getFolder() != Config::FOLDER_SPAM) {
            $options[] = [
                'id' => 'spam-button',
                'label' => __('To Spam'),
                'onclick' => "setLocation('" . $this->getSpamUrl() . "')",
            ];
        }
        if ($this->helpdeskPermission->isTicketRemoveAllowed()) {
            $options[] = [
                'id' => 'delete-button',
                'label' => __('Delete'),
                'class' => 'delete',
                'onclick' => 'deleteConfirm(\''.__(
                    'Are you sure you want to do this?'
                ).'\', \''.$this->getDeleteUrl().'\')',
            ];
        }
        $this->getToolbar()->addChild(
            'save-split-button',
            'Magento\Backend\Block\Widget\Button\SplitButton',
            [
                'id' => 'save-split-button',
                'label' => __('Save'),
                'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                'button_class' => 'widget-button-save',
                'options' => $options,
            ]
        );
        if ($ticket->getFolder() != Config::FOLDER_INBOX) {
            $this->buttonList->add('restore-button', [
                'id' => 'restore-button',
                'label' => __('To Inbox'),
                'onclick' => "setLocation('".$this->getRestoreUrl()."')",
                'class' => 'primary',
            ], -100);
        }
    }

    /**
     * @return string
     */
    public function getRmaUrl()
    {
        return $this->getUrl('rma/rma/convertticket', ['id' => $this->getTicket()->getId()]);
    }

    /**
     * @return string
     */
    public function getSpamUrl()
    {
        return $this->getUrl('*/*/spam', ['id' => $this->getTicket()->getId()]);
    }

    /**
     * @return string
     */
    public function getArchiveUrl()
    {
        return $this->getUrl('*/*/archive', ['id' => $this->getTicket()->getId()]);
    }

    /**
     * @return string
     */
    public function getRestoreUrl()
    {
        return $this->getUrl('*/*/restore', ['id' => $this->getTicket()->getId()]);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Ticket|false
     */
    public function getTicket()
    {
        if ($this->registry->registry('current_ticket') && $this->registry->registry('current_ticket')->getId()) {
            return $this->registry->registry('current_ticket');
        }
    }

    /************************/
}
