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



namespace Mirasvit\Helpdesk\Model\ResourceModel\Department;

/**
 * @method \Mirasvit\Helpdesk\Model\Department getFirstItem()
 * @method \Mirasvit\Helpdesk\Model\Department getLastItem()
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|
 *         \Mirasvit\Helpdesk\Model\Department[] addFieldToFilter
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\Mirasvit\Helpdesk\Model\Department[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'department_id';//@codingStandardsIgnoreLine

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
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     * @param array                                                        $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        array $data = []
    ) {
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
        $this->_init('Mirasvit\Helpdesk\Model\Department', 'Mirasvit\Helpdesk\Model\ResourceModel\Department');
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [
            ['value' => 0, 'label' => __('-- Please Select --')],
        ];

        /** @var \Mirasvit\Helpdesk\Model\Department $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Mirasvit\Helpdesk\Model\Department $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @return array
     */
    public function toProviderArray()
    {
        $arr = [];
        /** @var \Mirasvit\Helpdesk\Model\Department $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getData();
        }

        return $arr;
    }

    /**
     * @param int $userId
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\Mirasvit\Helpdesk\Model\Department[]
     */
    public function addUserFilter($userId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_helpdesk_department_user')}`
                AS `department_user_table`
                WHERE main_table.department_id = department_user_table.du_department_id
                AND department_user_table.du_user_id in (?))", [0, $userId]);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_helpdesk_department_store')}`
                AS `department_store_table`
                WHERE main_table.department_id = department_store_table.ds_department_id
                AND department_store_table.ds_store_id in (?))", [0, $storeId]);

        return $this;
    }

    /**
     * @param string $columnName
     * @return $this
     */
    public function addStoreColumn($columnName = 'store_ids')
    {
        $this->getSelect()
            ->columns(
                [$columnName => new \Zend_Db_Expr(
                    "(SELECT GROUP_CONCAT(ds_store_id) FROM `{$this->getTable('mst_helpdesk_department_store')}`
                    AS `department_store_table`
                    WHERE main_table.department_id = department_store_table.ds_department_id)")]
            );

        return $this;
    }

    /**
     *
     */
    protected function initFields()
    {
        $select = $this->getSelect();
        $select->order(new \Zend_Db_Expr('sort_order ASC'));
    }

    /**
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->initFields();
    }

    /**
     * @var int
     */
    protected $storeId;

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     *
     */
    public function _afterLoad()
    {
        if ($this->storeId) {
            foreach ($this as $item) {
                $item->setStoreId($this->storeId);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(\Magento\Framework\DataObject $item)
    {
        $item->setStoreIds(explode(',', $item->getStoreIds()));

        return parent::addItem($item);
    }

     /************************/
}
