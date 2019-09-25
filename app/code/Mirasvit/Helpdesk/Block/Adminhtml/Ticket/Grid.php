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

/**
 * Grid of tickets.
 * Used only in other tickets views.
 * Main grid is created using .xml files.
 *
 * @method bool getTabMode()
 * @method $this setTabMode(bool $param)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var array
     */
    protected $customFilters = [];

    /**
     * @var array
     */
    protected $removeFilters = [];

    /**
     * @var object
     */
    protected $activeTab;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory
     */
    protected $priorityCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Model\Status
     */
    protected $status;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission
     */
    protected $helpdeskPermission;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;

    /**
     * @var \Mirasvit\Helpdesk\Helper\User
     */
    protected $helpdeskUser;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Html
     */
    protected $helpdeskHtml;

    /**
     * @param \Mirasvit\Helpdesk\Model\TicketFactory                              $ticketFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory     $statusCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory   $priorityCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                     $config
     * @param \Mirasvit\Helpdesk\Model\Status                                     $status
     * @param \Mirasvit\Helpdesk\Helper\Permission                                $helpdeskPermission
     * @param \Mirasvit\Helpdesk\Helper\Field                                     $helpdeskField
     * @param \Mirasvit\Helpdesk\Helper\StringUtil                                    $helpdeskString
     * @param \Mirasvit\Helpdesk\Helper\User                                      $helpdeskUser
     * @param \Mirasvit\Helpdesk\Helper\Html                                      $helpdeskHtml
     * @param \Magento\Framework\Registry                                         $registry
     * @param \Magento\Backend\Block\Widget\Context                               $context
     * @param \Magento\Backend\Helper\Data                                        $backendHelper
     * @param array                                                               $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\Status $status,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Mirasvit\Helpdesk\Helper\User $helpdeskUser,
        \Mirasvit\Helpdesk\Helper\Html $helpdeskHtml,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
        $this->config = $config;
        $this->status = $status;
        $this->helpdeskPermission = $helpdeskPermission;
        $this->helpdeskField = $helpdeskField;
        $this->helpdeskString = $helpdeskString;
        $this->helpdeskUser = $helpdeskUser;
        $this->helpdeskHtml = $helpdeskHtml;
        $this->registry = $registry;
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('helpdesk_grid');
        $this->setDefaultSort('last_activity');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @param string     $field
     * @param bool|false $filter
     * @return $this
     */
    public function addCustomFilter($field, $filter = false)
    {
        if ($filter) {
            $this->customFilters[$field] = $filter;
        } else {
            $this->customFilters[] = $field;
        }

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function removeFilter($field)
    {
        $this->removeFilters[$field] = true;

        return $this;
    }

    /**
     * @return array|string
     */
    public function getFormattedNumberOfTickets()
    {
        $allN = $this->getNumberOfTickets();
        $activeN = $this->getNumberOfActiveTickets();
        $number = [];
        if ($activeN) {
            $number[] = "<b>$activeN</b>";
        }
        if ($allN == 0 || $allN > $activeN) {
            $number[] = "$allN";
        }
        $number = implode('/', $number);

        return $number;
    }

    /**
     * @return int
     */
    private function getNumberOfTickets()
    {
        return $this->getCollection()->count();
    }

    /**
     * @return int
     */
    private function getNumberOfActiveTickets()
    {
        $n = 0;
        foreach ($this->getCollection() as $ticket) {
            /** @var \Mirasvit\Helpdesk\Model\Ticket $ticket */
            if (!$ticket->isClosed()) {
                ++$n;
            }
        }

        return $n;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->ticketFactory->create()
            ->getCollection()
            ->addFieldToFilter('folder', ['neq' => Config::FOLDER_SPAM])
            ->joinColors();

        $this->helpdeskPermission->setTicketRestrictions($collection);
        foreach ($this->customFilters as $key => $value) {
            if ((int) $key === $key && is_string($value)) {
                $collection->getSelect()->where($value);
            } else {
                $collection->addFieldToFilter($key, $value);
            }
        }
        if ($helpdeskUser = $this->helpdeskUser->getHelpdeskUser()) {
            if ($helpdeskUser->getStoreId()) {
                $collection->addFieldToFilter('store_id', $helpdeskUser->getStoreId());
            }
        }

        //         echo $collection->getSelect();die;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @param object $collection
     * @param object $column
     *
     * @return void
     */
    protected function _filterSearchCondition(
        $collection,
        $column
    ) {
        if (!$query = $column->getFilter()->getValue()) {
            return;
        }
        $this->registry->register('helpdesk_search_query', $query);
        /** @var \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->getCollection();
        $collection->getSearchInstance()->joinMatched($query, $collection, 'main_table.ticket_id');
    }

    /**
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        //probably we can simplify this code. remove unused.
        $columns = [
            'code',
            'subject',
            'status_id',
            'priority_id',
            'user_id',
            'reply_cnt',
            'last_reply_at'
        ];

        if (in_array('code', $columns)) {
            $this->addColumn('code', [
                    'header' => __('ID'),
                    'align' => 'left',
                    'width' => '110px',
                    'index' => 'code',
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('subject', $columns)) {
            $this->addColumn('subject', [
                    'header' => __('Subject'),
                    'index' => 'subject',
                ]);
        }
        if (in_array('customer_name', $columns) && !$this->getTabMode()) {
            $this->addColumn('customer_name', [
                    'header' => __('Customer Name'),
                    'index' => 'customer_name',
                ]);
        }
        if (in_array('last_reply_name', $columns)) {
            $this->addColumn('last_reply_name', [
                    'header' => __('Last Replier'),
                    'index' => 'last_reply_name',
                ]);
        }
        if (in_array('user_id', $columns)) {
            $this->addColumn('user_id', [
                    'header' => __('Owner'),
                    'index' => 'user_id',
                    'type' => 'options',
                    'options' => $this->helpdeskHtml->getAdminUserOptionArray(),
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('department_id', $columns) && !$this->getTabMode()) {
            $collection = $this->departmentCollectionFactory->create();
            $this->helpdeskPermission->setDepartmentRestrictions($collection);
            $this->addColumn('department_id', [
                    'header' => __('Department'),
                    'index' => 'department_id',
                    'sort_index' => 'department.sort_order',
                    'type' => 'options',
                    'options' => $collection->getOptionArray(),
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('store_id', $columns) && !$this->getTabMode()) {
            $this->addColumn('store_id', [
                    'header' => __('Store'),
                    'index' => 'store_id',
                    'type' => 'options',
                    'options' => $this->helpdeskHtml->getCoreStoreOptionArray(),
                ]);
        }
        if (in_array('status_id', $columns)) {
            $this->addColumn('status_id', [
                    'header' => __('Status'),
                    'index' => 'status_id',
                    'sort_index' => 'status.sort_order',
                    'type' => 'options',
                    'options' => $this->statusCollectionFactory->create()->getOptionArray(),
                    'renderer' => '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid\Renderer\Highlight',
                ]);
        }
        if (in_array('priority_id', $columns)) {
            $this->addColumn('priority_id', [
                    'header' => __('Priority'),
                    'index' => 'priority_id',
                    'sort_index' => 'priority.sort_order',
                    'type' => 'options',
                    'options' => $this->priorityCollectionFactory->create()->getOptionArray(),
                    'renderer' => '\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid\Renderer\Highlight',
                ]);
        }
        if (in_array('reply_cnt', $columns) && !$this->getTabMode()) {
            $this->addColumn('reply_cnt', [
                    'header' => __('Replies'),
                    'index' => 'reply_cnt',
                    'type' => 'text',
                    'align' => 'center',
                ]);
        }
        if (in_array('mailing_date', $columns) && !$this->getTabMode()) {
            $this->addColumn('mailing_date', [
                    'header'           => __('Email was send'),
                    'index'            => 'mailing_date',
                    'type'             => 'datetime',
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('created_at', $columns) && !$this->getTabMode()) {
            $this->addColumn('created_at', [
                    'header' => __('Created At'),
                    'index' => 'created_at',
                    'type' => 'datetime',
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('updated_at', $columns) && !$this->getTabMode()) {
            $this->addColumn('updated_at', [
                    'header' => __('Updated At'),
                    //          'align'     => 'right',
                    //          'width'     => '50px',
                    'index' => 'updated_at',
                    'filter_index' => 'main_table.updated_at',
                    'type' => 'datetime',
                ]);
        }
        if (in_array('last_reply_at', $columns) && !$this->getTabMode()) {
            $this->addColumn('last_reply_at', [
                    'header' => __('Last Reply At'),
                    'index' => 'last_reply_at',
                    'type' => 'datetime',
                    'column_css_class' => 'nowrap',
                ]);
        }
        if (in_array('last_activity', $columns)) {
            $this->addColumn('last_activity', [
                    'header' => __('Last Activity'),
                    'index' => 'last_reply_at',
                    'type' => 'text',
                    'column_css_class' => 'nowrap',
                    'frame_callback' => [$this, '_lastActivityFormat'],
                ]);
        }

        $collection = $this->helpdeskField->getStaffCollection();
        foreach ($collection as $field) {
            if (in_array($field->getCode(), $columns)) {
                $this->addColumn($field->getCode(), [
                    'header' => __($field->getName()),
                    'index' => $field->getCode(),
                    'type' => $field->getGridType(),
                    'options' => $field->getGridOptions(),
                ]);
            }
        }

        if ($this->getTabMode() || in_array('action', $columns)) {
            $this->addColumn(
                'action',
                [
                    'header' => __('Action'),
                    'width' => '50px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('View'),
                            'url' => [
                                'base' => 'helpdesk/ticket/edit',
                            ],
                            'target' => '_blank',
                            'field' => 'id',
                        ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                ]
            );
        }

        return parent::_prepareColumns();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ?
                $column->getFilterIndex() : $column->getIndex();
            $columnIndex = $column->getSortIndex() ?
                $column->getSortIndex() : $columnIndex;
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }

        return $this;
    }

    /**
     * @param string                                    $renderedValue
     * @param object                                     $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool                                      $isExport
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _lastActivityFormat($renderedValue, $row, $column, $isExport)
    {
        $timestamp = strtotime($renderedValue);
        $diff = time() - $timestamp;

        $cssClass = 'last-activity';

        if ($diff < 60 * 60) {
            $cssClass .= ' _1h';
        } elseif ($diff < 3 * 60 * 60) {
            $cssClass .= ' _3h';
        } elseif ($diff < 12 * 60 * 60) {
            $cssClass .= ' _12h';
        } elseif ($diff < 24 * 60 * 60) {
            $cssClass .= ' _24h';
        } elseif ($diff < 2 * 24 * 60 * 60) {
            $cssClass .= ' _2d';
        } elseif ($diff < 3 * 24 * 60 * 60) {
            $cssClass .= ' _3d';
        } elseif ($diff) {
            $cssClass .= ' _5d';
        }

        return '<span class="'.$cssClass.'">'.$this->helpdeskString->nicetime($timestamp).'</span>';
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        if ($this->getTabMode()) {
            return $this;
        }
        $this->setMassactionIdField('ticket_id');
        $this->getMassactionBlock()->setFormFieldName('ticket_id');

        $this->getMassactionBlock()->addItem('status', [
            'label' => __('Change Status'),
            'url' => $this->getUrl('*/*/massChange', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Status'),
                    'values' => $this->status->toOptionArray(),
                ],
            ],
        ]);

        $this->getMassactionBlock()->addItem('owner', [
            'label' => __('Change Owner'),
            'url' => $this->getUrl('*/*/massChange', ['_current' => true]),
            'additional' => [
                'visibility' => [
                    'name' => 'owner',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => __('Owner'),
                    'values' => $this->helpdeskHtml->getAdminOwnerOptionArray(),
                ],
            ],
        ]);

        $this->getMassactionBlock()->addItem('Merge', [
            'label' => __('Merge'),
            'url' => $this->getUrl('*/*/massMerge'),
            'confirm' => __('Are you sure? This action is not reversible.'),
        ]);

        $this->getMassactionBlock()->addItem('archive', [
            'label' => __('Archive'),
            'url' => $this->getUrl('*/*/massChange', ['archive' => 1]),
            'confirm' => __('Are you sure?'),
        ]);

        $this->getMassactionBlock()->addItem('spam', [
            'label' => __('Mark as spam'),
            'url' => $this->getUrl('*/*/massChange', ['spam' => 1]),
            'confirm' => __('Are you sure?'),
        ]);

        if ($this->helpdeskPermission->isTicketRemoveAllowed()) {
            $this->getMassactionBlock()->addItem('delete', [
                'label' => __('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]);
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'helpdesk/ticket/edit',
            ['id' => $row->getId(),
            ]
        );
    }

    /**
     * @param string $tabName
     *
     * @return void
     */
    public function setActiveTab($tabName)
    {
        $this->activeTab = $tabName;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->activeTab) {
            return parent::getGridUrl().'?active_tab='.$this->activeTab;
        }

        return parent::getGridUrl();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            $field = ($column->getFilterIndex()) ? $column->getFilterIndex() : $column->getIndex();
            if ($column->getFilterConditionCallback()) {
                call_user_func($column->getFilterConditionCallback(), $this->getCollection(), $column);
            } else {
                $cond = $column->getFilter()->getCondition();
                if ($field && isset($cond)) {
                    $this->getCollection()->addFieldToFilter('main_table.'.$field, $cond);
                }
            }
        }

        return $this;
    }
}
