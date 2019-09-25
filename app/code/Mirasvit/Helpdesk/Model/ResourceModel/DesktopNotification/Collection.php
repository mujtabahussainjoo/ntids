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



namespace Mirasvit\Helpdesk\Model\ResourceModel\DesktopNotification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'notification_id';//@codingStandardsIgnoreLine

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission
     */
    protected $helpdeskPermission;

    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var object
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Permission                            $helpdeskPermission
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface       $entityFactory
     * @param \Psr\Log\LoggerInterface                                        $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface    $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                       $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface                  $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb            $resource
     * @param array                                                           $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        array $data = []
    ) {
        $this->helpdeskPermission = $helpdeskPermission;
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Mirasvit\Helpdesk\Model\DesktopNotification',
            'Mirasvit\Helpdesk\Model\ResourceModel\DesktopNotification'
        );
    }

    /**
     * @return $this|\Mirasvit\Helpdesk\Model\Ticket[]
     */
    public function joinTickets()
    {
        $this->getSelect()->joinInner(
            ['ticket' => $this->getTable('mst_helpdesk_ticket')],
            'main_table.ticket_id = ticket.ticket_id'
        );

        return $this;
    }

    /**
     * @param \Magento\User\Model\User $user
     * @return object
     */
    public function getMessagesCollection($user)
    {
        /** @var $this $collection */
        $collection = $this->joinTickets();

        $select = $collection->getSelect();
        if (!$permission = $this->helpdeskPermission->getPermission()) {
            $select->where('ticket.department_id', -1);
        }
        $departmentIds = $permission->getDepartmentIds();

        if (!in_array(0, $departmentIds)) {
            $select->where('ticket.department_id IN ('.implode(',', $departmentIds).')');
        }

        // add 1 hour to current gmt date
        $date = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');

        $select->where('main_table.created_at < ?', $date);

        $select->where(
            'read_by_user_ids NOT LIKE "%,' . $user->getId() . ',%" ' . //user has not read this notification before
            //notification is about new message of ticket of this user
            'AND (
                (
                    notification_type = "' . \Mirasvit\Helpdesk\Model\Config::NOTIFICATION_TYPE_NEW_MESSAGE . '"
                    AND (ticket.user_id = ' . $user->getId() . ' ) ' .
                ') ' .
            //or notification about something else
                'OR notification_type <> "' . \Mirasvit\Helpdesk\Model\Config::NOTIFICATION_TYPE_NEW_MESSAGE . '"' .
            ')'
        );

        return $collection;
    }
}
