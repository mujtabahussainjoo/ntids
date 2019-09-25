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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Model\ResourceModel\Article;

use Mirasvit\Kb\Model\Article;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection implements
    \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'article_id';//@codingStandardsIgnoreLine

    /**
     * @var \Mirasvit\Kb\Model\SearchFactory
     */
    protected $searchFactory;

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory
     */
    protected $articleCollectionFactory;

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
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|string|null
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @param \Mirasvit\Kb\Model\SearchFactory $searchFactory
     * @param CollectionFactory $articleCollectionFactory
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mirasvit\Kb\Helper\Data $kbData
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Kb\Model\SearchFactory $searchFactory,
        CollectionFactory $articleCollectionFactory,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Kb\Helper\Data $kbData,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->searchFactory = $searchFactory;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;
        $this->kbData = $kbData;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\Article', 'Mirasvit\Kb\Model\ResourceModel\Article', 'article_id');
    }

    /**
     * @todo ??
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('article_id', 'name');
    }

    /**
     * @todo ??
     * @return array
     */
    public function getOptionArray()
    {
        $arr = [];
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreIdFilter($storeId)
    {
        $rootId = (int)$this->kbData->getRootCategory($storeId)->getId();
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_kb_article_category')}`
                AS `article_category_table`
                INNER JOIN `{$this->getTable('mst_kb_category')}` `category_table`
                    ON category_table.category_id = article_category_table.ac_category_id
                LEFT JOIN `{$this->getTable('mst_kb_article_store')}` `article_store`
                    ON article_store.as_article_id = article_category_table.ac_article_id
                WHERE main_table.article_id = article_category_table.ac_article_id
                    AND (category_table.path LIKE '%/$rootId/%' OR category_table.category_id = $rootId)
                    AND (article_store.as_store_id IN ($storeId, 0) OR article_store.as_store_id IS NULL)
                )");

        return $this;
    }

    /**
     * @return $this
     */
    public function joinStoreIds()
    {
        $this->getSelect()
            ->joinLeft(
                ['article_store' => $this->getTable('mst_kb_article_store')],
                'main_table.article_id = article_store.as_article_id',
                ['store_id' => new \Zend_Db_Expr('COALESCE(GROUP_CONCAT(article_store.as_store_id), 0)')]
            )
            ->group('main_table.article_id');

        return $this;
    }

    /**
     * @return $this
     */
    public function joinCategoryIds()
    {
        $this->getSelect()
            ->joinLeft(
                ['article_category' => $this->getTable('mst_kb_article_category')],
                'main_table.article_id = article_category.ac_article_id',
                ['category_id' => new \Zend_Db_Expr('COALESCE(GROUP_CONCAT(article_category.ac_category_id), 0)')]
            )
            ->group('main_table.article_id');

        return $this;
    }

    /**
     * @param int $categoryId
     *
     * @return $this
     */
    public function addCategoryIdFilter($categoryId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_kb_article_category')}`
                AS `article_category_table`
                WHERE main_table.article_id = article_category_table.ac_article_id
                AND article_category_table.ac_category_id in (?))", [0, $categoryId]);

        return $this;
    }

    /**
     * @param int $tagId
     *
     * @return $this
     */
    public function addTagIdFilter($tagId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_kb_article_tag')}`
                AS `article_tag_table`
                WHERE main_table.article_id = article_tag_table.at_article_id
                AND article_tag_table.at_tag_id in (?))", [0, $tagId]);

        return $this;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function addCustomerGroupIdFilter($groupId)
    {
        $this->getSelect()
            ->where("EXISTS (
                    SELECT *
                    FROM `{$this->getTable('mst_kb_article_customer_group')}` AS `article_customer_group_table`
                    WHERE main_table.article_id = article_customer_group_table.acg_article_id
                        AND (article_customer_group_table.acg_group_id = ? 
                            OR article_customer_group_table.acg_group_id = '" . Article::ALL_GROUPS_KEY . "'))
                    OR NOT EXISTS (
                    SELECT *
                    FROM `{$this->getTable('mst_kb_article_customer_group')}` AS `article_customer_group_table`
                    WHERE main_table.article_id = article_customer_group_table.acg_article_id
                )", $groupId);

        return $this;
    }

    /**
     * @return $this
     */
    public function addVisibilityFilter()
    {
        $this->getSelect()
            ->where("main_table.is_active = 1");

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $select = $this->getSelect();
        $select->joinLeft(
            ['user' => $this->getTable('admin_user')],
            'main_table.user_id = user.user_id',
            ['user_name' => 'CONCAT(firstname, " ", lastname)']
        );
        $select->columns(['rating' => new \Zend_Db_Expr('votes_sum/votes_num')]);

        parent::_initSelect();
    }

    /**
     * @return \Mirasvit\Kb\Model\Search
     */
    public function getSearchInstance()
    {
        $collection = $this->articleCollectionFactory->create();

        $collection = $this->addTagSearch($collection);

        $search = $this->searchFactory->create();
        $search->setSearchableCollection($collection);
        $search->setSearchableAttributes([
            'main_table.article_id'       => 0,
            'main_table.name'             => 100,
            'main_table.text'             => 50,
            'main_table.meta_title'       => 80,
            'main_table.meta_keywords'    => 80,
            'main_table.meta_description' => 60,
            'tag_name'                    => 55,
        ]);
        $search->setPrimaryKey('article_id');

        return $search;
    }

    /**
     * @param \Mirasvit\Kb\Model\ResourceModel\Article\Collection $collection
     * @return \Mirasvit\Kb\Model\ResourceModel\Article\Collection
     */
    private function addTagSearch($collection)
    {
        $tagTable        = $collection->getTable('mst_kb_tag');
        $tagAlias        = 'tag';
        $articleTagTable = $collection->getTable('mst_kb_article_tag');
        $articleTagAlias = 'article_tag';

        $collection->getSelect()
            ->joinLeft(
                [$articleTagAlias => $articleTagTable],
                $articleTagAlias.'.at_article_id = main_table.article_id',
                ['']
            )->joinLeft(
                [$tagAlias => $tagTable],
                $tagAlias.'.tag_id = '.$articleTagAlias.'.at_tag_id',
                ['tag_name' => $tagAlias.'.name']
            )->group('main_table.article_id');

        return $collection;
    }
}
