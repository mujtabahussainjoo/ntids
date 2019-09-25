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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime;

class Article extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
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
     * Application Cache Manager.
     *
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cacheManager;

    /**
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface      $urlRewrite
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\CacheInterface             $cacheManager
     * @param null                                              $resourcePrefix
     */
    public function __construct(
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Mirasvit\Kb\Model\Config $config,
        $resourcePrefix = null
    ) {
        $this->urlRewrite = $urlRewrite;
        $this->context = $context;
        $this->cacheManager = $cacheManager;
        $this->resourcePrefix = $resourcePrefix;
        $this->config = $config;

        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_kb_article', 'article_id');
    }

    /**
     * @param AbstractModel $article
     *
     * @return AbstractModel
     */
    protected function loadStoreIds(AbstractModel $article)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_kb_article_store'))
            ->where('as_article_id = ?', $article->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['as_store_id'];
            }
            $article->setData('store_ids', $array);
        }

        return $article;
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return $this
     */
    protected function saveStoreIds($article)
    {
        $condition = $this->getConnection()->quoteInto('as_article_id = ?', $article->getId());
        $this->getConnection()->delete($this->getTable('mst_kb_article_store'), $condition);
        foreach ((array)$article->getData('store_ids') as $id) {
            $objArray = [
                'as_article_id' => $article->getId(),
                'as_store_id'   => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_kb_article_store'),
                $objArray
            );
        }

        return $this;
    }

    /**
     * @param AbstractModel $article
     *
     * @return AbstractModel
     */
    protected function loadCategoryIds(AbstractModel $article)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_kb_article_category'))
            ->where('ac_article_id = ?', $article->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['ac_category_id'];
            }
            $article->setData('category_ids', $array);
        } else {
            $article->setData('category_ids', []);
        }

        return $article;
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return void
     */
    protected function saveCategoryIds($article)
    {
        $condition = $this->getConnection()->quoteInto('ac_article_id = ?', $article->getId());
        $this->getConnection()->delete($this->getTable('mst_kb_article_category'), $condition);
        foreach ((array)$article->getData('category_ids') as $id) {
            if ($id) {
                $objArray = [
                    'ac_article_id'  => $article->getId(),
                    'ac_category_id' => $id,
                ];
                $this->getConnection()->insert(
                    $this->getTable('mst_kb_article_category'),
                    $objArray
                );
            }
        }
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return \Mirasvit\Kb\Model\Article
     */
    protected function loadCustomerCategoryIds(\Mirasvit\Kb\Model\Article $article)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_kb_article_customer_group'))
            ->where('acg_article_id = ?', $article->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['acg_group_id'];
            }
            $article->setData('customer_group_ids', $array);
        }

        return $article;
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return void
     */
    protected function saveCustomerCategoryIds($article)
    {
        $condition = $this->getConnection()->quoteInto('acg_article_id = ?', $article->getId());
        $this->getConnection()->delete($this->getTable('mst_kb_article_customer_group'), $condition);
        foreach ((array)$article->getData('customer_group_ids') as $id) {
            $objArray = [
                'acg_article_id' => $article->getId(),
                'acg_group_id'   => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_kb_article_customer_group'),
                $objArray
            );
        }
    }

    /**
     * @param AbstractModel $article
     * @return AbstractModel
     */
    protected function loadTagIds(AbstractModel $article)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_kb_article_tag'))
            ->where('at_article_id = ?', $article->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['at_tag_id'];
            }
            $article->setData('tag_ids', $array);
        }

        return $article;
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @return void
     */
    protected function saveTagIds($article)
    {
        $condition = $this->getConnection()->quoteInto('at_article_id = ?', $article->getId());
        $this->getConnection()->delete($this->getTable('mst_kb_article_tag'), $condition);
        foreach ((array)$article->getData('tag_ids') as $id) {
            $objArray = [
                'at_article_id' => $article->getId(),
                'at_tag_id'     => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_kb_article_tag'),
                $objArray
            );
        }
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadStoreIds($object);
        $this->loadCategoryIds($object);
        $this->loadTagIds($object);
        $this->loadCustomerCategoryIds($object);

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \Mirasvit\Kb\Model\Article $object */

        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));

        if (!$urlKey = $object->getUrlKey()) {
            $urlKey = $object->getName();
        }
        $object->setUrlKey($this->urlRewrite->normalize($urlKey));

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        /** @var \Mirasvit\Kb\Model\Article $object */
        $this->saveStoreIds($object);
        $this->saveCategoryIds($object);
        $this->saveTagIds($object);
        $this->saveCustomerCategoryIds($object);

        foreach ((array)$object->getData('store_ids') as $id) {
            $categories = $object->getCategories($id);
            /** @var \Mirasvit\Kb\Model\Category $category */
            foreach ($categories as $category) {
                $this->cacheManager->clean([$object::CACHE_KB_ARTICLE_CATEGORY . '_' . $category->getId()]);
                $categoryKey = '';
                if (!$this->config->getCategoryURLExcluded()) {
                    $categoryKey = $category->getUrlKey();
                }
                $this->urlRewrite->updateUrlRewrite(
                    'KBASE',
                    'ARTICLE',
                    $object,
                    [
                        'article_key' => $object->getUrlKey(),
                        'category_key' => $categoryKey
                    ],
                    $id
                );
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $this->urlRewrite->deleteUrlRewrite('KBASE', 'ARTICLE', $object);

        return parent::_afterDelete($object);
    }
}
