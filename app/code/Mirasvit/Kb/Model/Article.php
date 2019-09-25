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
use Magento\Framework\Model\AbstractModel;

/**
 * @method string getText()
 * @method string getMetaTitle()
 * @method string getMetaKeywords()
 * @method string getMetaDescription()
 * @method bool getIsActive()
 * @method string getUrlKey()
 * @method int getUserId()
 * @method int getVotesSum()
 * @method int getVotesNum()
 * @method int getPosition()
 */
class Article extends AbstractModel implements IdentityInterface
{
    const ALL_GROUPS_KEY = 'all';

    const CACHE_TAG = 'kb_category_article';

    /**
     * @var string
     */
    protected $_eventPrefix = 'kb_article';//@codingStandardsIgnoreLine

    /**
     * Category product relation cache tag.
     */
    const CACHE_KB_ARTICLE_CATEGORY = 'kb_category_article';

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CategoryFactory $categoryFactory,
        \Magento\User\Model\UserFactory $userFactory,
        ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry
    ) {
        $this->storeManager              = $storeManager;
        $this->categoryFactory           = $categoryFactory;
        $this->userFactory               = $userFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->tagCollectionFactory      = $tagCollectionFactory;
        $this->urlRewrite                = $urlRewrite;
        $this->filterProvider            = $filterProvider;
        $this->context                   = $context;
        $this->registry                  = $registry;

        parent::__construct($context, $registry);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\ResourceModel\Article');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [
            self::CACHE_TAG . '_' . $this->getId(),
        ];

        if ($this->hasDataChanges() || $this->isDeleted()) {
            $category = $this->getCategory();
            if ($category) {
                $identities[] = self::CACHE_KB_ARTICLE_CATEGORY . '_' . $category->getId();
                $ids = $category->getParentIds();
                foreach ($ids as $id) {
                    $identities[] = self::CACHE_KB_ARTICLE_CATEGORY . '_' . $id;
                }
            }
        }

        return $identities;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->urlRewrite->getUrl('KBASE', 'ARTICLE', $this);
    }

    /**
     * @return array
     */
    public function getStoreIds()
    {
        return (array)$this->getData('store_ids');
    }

    /**
     * @return array
     */
    public function getCategoryIds()
    {
        $ids = $this->getData('category_ids');
        if (is_array($ids) && count($ids)) {
            if (($key = array_search(1, $ids)) !== false) { //REMOVE SUPER ROOT CATEGORY
                unset($ids[$key]);
            }

            return $ids;
        } else {
            return [];
        }
    }

    /**
     * @param int $categoryId
     * @return $this
     */
    public function deleteCategoryId($categoryId)
    {
        $ids = $this->getData('category_ids');
        if (count($ids)) {
            foreach ($ids as $key => $id) {
                if ($id == $categoryId) {
                    unset($ids[$key]);
                }
            }
            $this->setData('category_ids', $ids);
        }

        return $this;
    }

    /**
     * @return Category|false
     */
    public function getCategory()
    {
        $ids = $this->getCategoryIds();
        if (count($ids) == 0) {
            return false;
        }
        $category = $this->getCategories()->getFirstItem();

        return $category;
    }

    /**
     * @param int $storeId
     * @return ResourceModel\Category\Collection
     */
    public function getCategories($storeId = 0)
    {
        if (!$storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('category_id', array_merge([0], $this->getCategoryIds()))
            ->addCategoryStoreIdFilter($storeId)
        ;

        return $collection;
    }

    /**
     * @return ResourceModel\Tag\Collection
     */
    public function getTags()
    {
        $collection = $this->tagCollectionFactory->create()
            ->addFieldToFilter('tag_id', $this->getTagIds());

        return $collection;
    }

    /**
     * @return \Magento\User\Model\User
     */
    public function getUser()
    {
        $user = $this->userFactory->create()->load($this->getUserId());

        return $user;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        if ($this->getVotesNum()) {
            return $this->getVotesSum() / $this->getVotesNum();
        }

        return 0;
    }

    /**
     * @param int $vote
     *
     * @return $this
     */
    public function addVote($vote)
    {
        $this->setVotesNum($this->getVotesNum() + 1)
            ->setVotesSum($this->getVotesSum() + $vote);

        return $this;
    }

    /**
     * @return float
     */
    public function getPositiveVoteNum()
    {
        $sum = $this->getVotesSum();
        $amount = $this->getVotesNum();

        return round($amount - (5 * $amount - $sum) / 4);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getTextHtml()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getText());
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
     * @return string
     *
     * @throws \Exception
     */
    public function getSummary()
    {
        $html = $this->getTextHtml();

        $html = preg_replace('/<table[^>]*>.*<\/table>/is', '', $html);

        return $html;
    }
}
