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



namespace Mirasvit\Kb\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method ResourceModel\Category getResource()
 * @method string getUrlKey()
 * @method string getMetaTitle()
 * @method string getMetaKeywords()
 * @method string getMetaDescription()
 * @method bool getIsActive()
 * @method array getStoreIds()
 * @method int getSortOrder()
 * @method int getParentId()
 * @method string getPath()
 * @method int getLevel()
 * @method int getPosition()
 * @method int getChildrenCount()
 * @method string getDisplayMode()
 * @method string getDescription()
 * @method string getCustomLayoutUpdate()
 */
class Category extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'kb_category_article';

    /**
     * @var string
     */
    protected $_cacheTag = 'kb_category_article';//@codingStandardsIgnoreLine

    /**
     * @var string
     */
    protected $_eventPrefix = 'kb_category';//@codingStandardsIgnoreLine

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->hasDataChanges() || $this->isDeleted()) {
            $identities[] = Article::CACHE_KB_ARTICLE_CATEGORY . '_' . $this->getId();
            $ids = $this->getParentIds();
            foreach ($ids as $id) {
                $identities[] = Article::CACHE_KB_ARTICLE_CATEGORY . '_' . $id;
            }
        }

        return $identities;
    }

    /**
     * @var \Mirasvit\Kb\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Mirasvit\Core\Api\UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @var \Mirasvit\Kb\Model\Config
     */
    protected $config;

    /**
     * @param \Mirasvit\Kb\Model\CategoryFactory                          $categoryFactory
     * @param \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface                $urlRewrite
     * @param \Magento\Framework\Model\Context                            $context
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Mirasvit\Kb\Model\Config                                   $config
     * @param \Magento\Cms\Model\Template\FilterProvider                  $filterProvider
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource     $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb               $resourceCollection
     * @param array                                                       $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Kb\Model\Config $config,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->urlRewrite = $urlRewrite;
        $this->context = $context;
        $this->registry = $registry;
        $this->filterProvider = $filterProvider;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        $this->config = $config;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\ResourceModel\Category');
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->isRootCategory()) {
            return $this->urlRewrite->getUrl('KBASE', 'CATEGORY_ROOT');
        } else {
            return $this->urlRewrite->getUrl('KBASE', 'CATEGORY', $this);
        }
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getDescriptionHtml()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getDescription());
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return count($this->getParentIds()) == 1;
    }

    /**
     * Get parent-root category of category.
     *
     * @return bool|int
     */
    public function getParentRootCategory()
    {
        $id = false;
        $ids = $this->getPathIds();
        if ($ids && count($ids) > 1) {
            $id = $ids[1];
        }

        return $id;
    }

    /**
     * Get all parent categories ids.
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->getChildrenCount() > 0 ? 1 : 0;
    }

    /**
     * @param null       $store
     *
     * @return \Mirasvit\Kb\Model\ResourceModel\Category\Collection
     */
    public function getChildren($store = null)
    {
        return $this->getAllChildren($store)
            ->addFieldToFilter('is_active', true);
    }

    /**
     * @param null       $store
     *
     * @return \Mirasvit\Kb\Model\ResourceModel\Category\Collection
     */
    public function getAllChildren($store = null)
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('parent_id', $this->getId())
            ->addFieldToFilter('level', $this->getLevel() + 1)
            ->setOrder('position', 'asc');

        if ($store) {
            $collection->addStoreIdFilter($store);
        }

        return $collection;
    }

    /**
     * @param string $type
     *
     * @return \Mirasvit\Kb\Model\ResourceModel\Category\Collection
     */
    public function getChildrenByType($type)
    {
        $collection = $this->getChildren()
            ->addFieldToFilter('type', $type);

        return $collection;
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function loadPathArray($path)
    {
        $result = [];
        $ids = explode('/', $path);
        foreach ($ids as $categoryId) {
            $result[] = $this->categoryFactory->create()->load($categoryId);
        }

        return $result;
    }

    /**
     * @param int $parentId
     * @param int $afterItemId
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function move($parentId, $afterItemId)
    {
        if ($parentId != null) {
            $parent = $this->categoryFactory->create()
                ->load($parentId);

            if (!$parent->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Item move operation is not possible: the new parent category was not found.')
                );
            }
        } else {
            $parent = $this->categoryFactory->create();
        }
        if (!$this->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Item move operation is not possible: the current category was not found.')
            );
        } elseif ($parent && $parent->getId() == $this->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Item move operation is not possible: parent category is equal to child category.')
            );
        }
        $this->setData('moved_item_id', $this->getId());
        $this->getResource()->changeParent($this, $parent, $afterItemId);

        return $this;
    }

    /**
     * @param int $customerGroupId
     * @return int
     */
    public function getArticlesNumber($customerGroupId)
    {
        return $this->getResource()->getArticlesNumber($this, $customerGroupId);
    }

    /**
     * @return bool
     */
    public function isReadonly()
    {
        return false;
    }

    /**
     * @param bool $filter
     * @return string
     */
    public function getName($filter = true)
    {
        if ($filter) {
            return __(parent::getName());
        } else {
            return parent::getName();
        }
    }

    /**
     * @return void
     */
    public function saveNewObject()
    {
        $this->getResource()->saveNewObjectWrapper($this);
    }
}
