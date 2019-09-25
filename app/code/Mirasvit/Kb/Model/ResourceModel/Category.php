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



namespace Mirasvit\Kb\Model\ResourceModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory
     */
    protected $articleCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Core\Api\UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string|null
     */
    protected $resourcePrefix;

    /**
     * @param \Mirasvit\Kb\Model\CategoryFactory                         $categoryFactory
     * @param \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface               $urlRewrite
     * @param \Magento\Framework\App\CacheInterface                      $cacheManager
     * @param \Magento\Framework\Model\ResourceModel\Db\Context          $context
     * @param null                                                       $resourcePrefix
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->urlRewrite = $urlRewrite;
        $this->context = $context;
        $this->resource = $context->getResources();
        $this->resourcePrefix = $resourcePrefix;
        $this->cacheManager = $cacheManager;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_kb_category', 'category_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $category)
    {
        if (!$category->getIsMassDelete()) {
            $this->loadStoreIds($category);
        }

        return parent::_afterLoad($category);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $category)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_kb_category_store'))
            ->where('as_category_id = ?', $category->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['as_store_id'];
            }
            $category->setData('store_ids', $array);
        }

        return $category;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $category)
    {
        /** @var \Mirasvit\Kb\Model\Category $category */
        if (!$category->getId()) {
            $category->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $category->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        if (!$urlKey = $category->getUrlKey()) {
            $urlKey = $category->getName(false);
        }

        $category->setUrlKey($this->urlRewrite->normalize($urlKey));

        if (!$category->getChildrenCount()) {
            $category->setChildrenCount(0);
        }

        if (!$category->getId()) {
            $parentId = $category->getParentId();
            $parentCategory = $this->categoryFactory->create()->load($parentId);
            $category->setPath($parentCategory->getPath());

            $category->setPosition($this->_getMaxPosition($category->getPath()) + 1);
            $level = count($category->getPathIds());
            $category->setLevel($level);

            if (!$category->getParentId()) { //for Root Category with ID = 1
                $category->setLevel(0);
            }

            $category->setPath($category->getPath().'/');

            $toUpdateChild = explode('/', $category->getPath());

            $this->getConnection()->update(
                $this->getTable('mst_kb_category'),
                ['children_count' => new \Zend_Db_Expr('children_count + 1')],
                ['category_id IN(?)' => $toUpdateChild]
            );
        }

        return parent::_beforeSave($category);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $category)
    {
        if (!$category->getIsMassStatus()) {
            $this->saveStoreIds($category);

            /** @var \Mirasvit\Kb\Model\Category $rootCategory */
            /** @var \Mirasvit\Kb\Model\Category $category */
            $rootCategory = $this->categoryFactory->create()->load($category->getParentRootCategory());
            if ($category->isRootCategory()) {
                $storeIds = (array)$category->getData('store_ids');
            } else {
                $storeIds = (array)$rootCategory->getData('store_ids');
            }
            foreach ($storeIds as $id) {
                $this->urlRewrite->updateUrlRewrite(
                    'KBASE',
                    'CATEGORY',
                    $category,
                    ['category_key' => $category->getUrlKey()],
                    $id
                );
            }

            //resave categorys URL key
            if ($category->getOrigData('url_key') != $category->getData('url_key')) {
                $articles = $this->articleCollectionFactory->create()
                                ->addCategoryIdFilter($category->getId())
                                ;
                foreach ($articles as $article) {
                    $article->afterLoad();
                    $article->save();
                }
            }

            if ($category->getPath() ==  '/') {
                $category->setPath($category->getId());
                $this->_savePath($category);
            } elseif (substr($category->getPath(), -1) == '/') {
                $category->setPath($category->getPath().$category->getId());
                $this->_savePath($category);
            }
        }

        return parent::_afterSave($category);
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     * @return void
     */
    protected function saveStoreIds($category)
    {
        $condition = $this->getConnection()->quoteInto('as_category_id = ?', $category->getId());
        $this->getConnection()->delete($this->getTable('mst_kb_category_store'), $condition);
        foreach ((array) $category->getData('store_ids') as $id) {
            $objArray = [
                'as_category_id' => $category->getId(),
                'as_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_kb_category_store'),
                $objArray
            );
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $category)
    {
        /** @var \Mirasvit\Kb\Model\Category $category */
        foreach ($category->getAllChildren() as $child) {
            $child->delete();
        }
        /** @var \Mirasvit\Kb\Model\Article $article */
        $articles = $this->articleCollectionFactory->create()
            ->addCategoryIdFilter($category->getId())
        ;
        foreach ($articles as $article) {
            $article->afterLoad();
            $article->deleteCategoryId($category->getId());
            $article->save();
        }

        return parent::_beforeDelete($category);
    }

    /**
     * @param int $categoryId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildrenCount($categoryId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), 'children_count')
            ->where('category_id = :category_id');
        $bind = ['category_id' => $categoryId];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     * @param \Mirasvit\Kb\Model\Category $newParent
     * @param null|int                    $afterItemId
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function changeParent($category, $newParent, $afterItemId = null)
    {
        $childrenCount = $this->getChildrenCount($category->getId()) + 1;
        $table = $this->getMainTable();
        $adapter = $this->getConnection();
        $levelFiled = $adapter->quoteIdentifier('level');
        $pathField = $adapter->quoteIdentifier('path');

        /*
         * Decrease children count for all old Item parent categories
         */
        $adapter->update(
            $table,
            ['children_count' => new \Zend_Db_Expr('children_count - '.$childrenCount)],
            ['category_id IN(?)' => $category->getParentIds()]
        );

        /*
         * Increase children count for new Item parents
         */
        $adapter->update(
            $table,
            ['children_count' => new \Zend_Db_Expr('children_count + '.$childrenCount)],
            ['category_id IN(?)' => $newParent->getPathIds()]
        );

        $position = $this->_processPositions($category, $newParent, $afterItemId);

        if ($newParent->getPath()) {
            $newPath = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        } else {
            $newPath = $category->getId();
        }
        $newLevel = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /*
         * Update children nodes path
         */
        $adapter->update(
            $table,
            [
                'path' => new \Zend_Db_Expr(
                    'REPLACE('.$pathField.','.
                    $adapter->quote($category->getPath().'/').', '.$adapter->quote($newPath.'/').')'
                ),
                'level' => new \Zend_Db_Expr($levelFiled.' + '.$levelDisposition),
            ],
            [$pathField.' LIKE ?' => $category->getPath().'/%']
        );

        /*
         * Update moved category data
         */
        $data = [
            'path' => $newPath,
            'level' => $newLevel,
            'position' => $position,
            'parent_id' => $newParent->getId(),
        ];
        $adapter->update($table, $data, ['category_id = ?' => $category->getId()]);

        $category->addData($data);

        return $this;
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _savePath($category)
    {
        if ($category->getId()) {
            $this->getConnection()->update(
                $this->getMainTable(),
                ['path' => $category->getPath()],
                ['category_id = ?' => $category->getId()]
            );
        }

        return $this;
    }

    /**
     * @param \Mirasvit\Kb\Model\Category $category
     * @param \Mirasvit\Kb\Model\Category $newParent
     * @param int                         $afterItemId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _processPositions($category, $newParent, $afterItemId)
    {
        $table = $this->getMainTable();
        $adapter = $this->getConnection();
        $positionField = $adapter->quoteIdentifier('position');

        $bind = [
            'position' => new \Zend_Db_Expr($positionField.' - 1'),
        ];
        $where = [
            'parent_id = ?' => $category->getParentId(),
            $positionField.' > ?' => $category->getPosition(),
        ];
        $adapter->update($table, $bind, $where);

        /*
         * Prepare position value
         */

        if ($afterItemId) {
            $select = $adapter->select()
                ->from($table, 'position')
                ->where('category_id = :category_id');
            $position = $adapter->fetchOne($select, ['category_id' => $afterItemId]);

            $bind = [
                'position' => new \Zend_Db_Expr($positionField.' + 1'),
            ];

            if (intval($newParent->getId()) == 0) {
                $where = [
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position,
                ];
            } else {
                $where = [
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position,
                ];
            }

            $adapter->update($table, $bind, $where);
        } elseif ($afterItemId !== null) {
            $position = 0;
            $bind = [
                'position' => new \Zend_Db_Expr($positionField.' + 1'),
            ];

            if (intval($newParent->getId()) == 0) {
                $where = [
                    'parent_id IS NULL',
                    $positionField.' > ?' => $position,
                ];
            } else {
                $where = [
                    'parent_id = ?' => $newParent->getId(),
                    $positionField.' > ?' => $position,
                ];
            }

            $adapter->update($table, $bind, $where);
        } else {
            $select = $adapter->select()
                ->from($table, ['position' => new \Zend_Db_Expr('MIN('.$positionField.')')])
                ->where('parent_id = :parent_id');
            $position = $adapter->fetchOne($select, ['parent_id' => $newParent->getId()]);
        }
        $position += 1;

        return $position;
    }

    /**
     * @param string $path
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getMaxPosition($path)
    {
        $adapter = $this->getConnection();
        $positionField = $adapter->quoteIdentifier('position');
        $level = count(explode('/', $path));
        if ($path == '') {
            $level = 1;
            $path = '%';
        } else {
            ++$level;
            $path .= '/%';
        }
        $bind = [
            'c_level' => $level,
            'c_path' => $path,
        ];
        $select = $adapter->select()
            ->from($this->getMainTable(), 'MAX('.$positionField.')')
            ->where($adapter->quoteIdentifier('path').' LIKE :c_path')
            ->where($adapter->quoteIdentifier('level').' = :c_level');
        $position = $adapter->fetchOne($select, $bind);

        if (!$position) {
            $position = 0;
        }

        return $position;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     *
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $category)
    {
        $this->urlRewrite->deleteUrlRewrite('KBASE', 'CATEGORY', $category);

        return parent::_afterDelete($category);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     * @param int                                    $customerGroupId
     *
     * @return int
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getArticlesNumber(\Magento\Framework\Model\AbstractModel $category, $customerGroupId)
    {
        $resource = $this->resource;

        $readConnection = $resource->getConnection('core_read');
        $query = "
        SELECT count(distinct ac_article_id) FROM {$this->getMainTable()} c
        LEFT JOIN {$resource->getTableName('mst_kb_article_category')} ac ON c.`category_id` = ac.`ac_category_id`
        LEFT JOIN {$resource->getTableName('mst_kb_article')} art ON ac_article_id = art.article_id
        WHERE path LIKE '{$category->getPath()}%' AND art.is_active=1 AND (
            EXISTS (
                SELECT *
                FROM {$resource->getTableName('mst_kb_article_customer_group')} AS `article_customer_group_table`
                WHERE art.article_id = article_customer_group_table.acg_article_id
                    AND article_customer_group_table.acg_group_id = {$customerGroupId}
            ) OR NOT EXISTS (
                SELECT *
                FROM {$resource->getTableName('mst_kb_article_customer_group')} AS `article_customer_group_table`
                WHERE art.article_id = article_customer_group_table.acg_article_id
            )
        )";

        $num = (int) $readConnection->fetchOne($query);

        return $num;
    }

    /**
     * Method used for tests. Allows to add objects with predefined ids.
     *
     * @param Category $category
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveNewObjectWrapper($category)
    {
        $this->_beforeSave($category);
        $bind = $this->_prepareDataForSave($category);
        $this->getConnection()->insert($this->getMainTable(), $bind);
        if ($this->_useIsObjectNew) {
            $category->isObjectNew(false);
        }
        $this->_afterSave($category);
    }
}
