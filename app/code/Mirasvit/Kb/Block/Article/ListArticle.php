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



namespace Mirasvit\Kb\Block\Article;

use Mirasvit\Kb\Model\Article;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ListArticle extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    /**
     * Default toolbar block name.
     *
     * @var string
     */
    protected $defaultToolbarBlock = 'Mirasvit\Kb\Block\Article\ArticleList\Toolbar';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Kb\Model\CategoryFactory $categoryFactory,
        \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Kb\Helper\Data $kbData,
        array $data = []
    ) {
        $this->customerSession          = $customerSession;
        $this->config                   = $config;
        $this->categoryFactory          = $categoryFactory;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->urlHelper                = $urlHelper;
        $this->coreRegistry             = $registry;
        $this->registry                 = $registry;
        $this->kbData                   = $kbData;
        $this->context                  = $context;

        parent::__construct($context, $data);
    }

    /**
     * Retrieve loaded category collection.
     *
     * @return AbstractCollection
     */
    public function getLoadedArticleCollection()
    {
        return $this->getArticleCollection();
    }

    /**
     * Retrieve current view mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result).
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getArticleCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'category_block_article_list_collection',
            ['collection' => $this->getArticleCollection()]
        );

        $this->setCollection($toolbar->getCollection());

        $this->getArticleCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block.
     *
     * @return \Mirasvit\Kb\Block\Article\ArticleList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    /**
     * Retrieve additional blocks html.
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML.
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->getArticleCollection()->addAttributeToSelect($code);

        return $this;
    }

    /**
     * Return identifiers for article content.
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getArticleCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        $category = $this->getCategory();
        if ($category) {
            $identities[] = Article::CACHE_KB_ARTICLE_CATEGORY.'_'.$category->getId();
        }

        return $identities;
    }

    /**
     * Retrieve current category model object.
     *
     * @return \Mirasvit\Kb\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('kb_current_category');
    }

    /**
     * Set current category model object.
     *
     * @param \Mirasvit\Kb\Model\ResourceModel\Category\Collection $category
     *
     * @return void
     */
    public function setCategory($category)
    {
        $this->registry->register('kb_current_category', $category);
    }

    /**
     * @return \Mirasvit\Kb\Model\Tag
     */
    public function getTag()
    {
        return $this->registry->registry('current_tag');
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->registry->registry('search_query');
    }

    /**
     * @return $this|\Mirasvit\Kb\Model\ResourceModel\Article\Collection
     */
    public function getArticleCollection()
    {
        $toolbar = $this->getToolbarBlock();

        if (empty($this->collection)) {
            $collection = $this->articleCollectionFactory->create()
                ->addFieldToFilter('main_table.is_active', true)
                ->addStoreIdFilter($this->context->getStoreManager()->getStore()->getId())
                ->addCustomerGroupIdFilter($this->customerSession->getCustomerGroupId())
            ;
            if ($category = $this->getCategory()) {
                $collection->addCategoryIdFilter($category->getId());
            } elseif ($tag = $this->getTag()) {
                $collection->addTagIdFilter($tag->getId());
            } elseif ($q = $this->getSearchQuery()) {
                $this->kbData->addSearchFilter($collection, $q);
            }

            $collection->setCurPage($this->getCurrentPage());

            $limit = (int) $toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }
            $page = (int) $toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }
            if ($order = $toolbar->getCurrentOrder()) {
                $collection->setOrder($order, $toolbar->getCurrentDirection());
            }
            $this->collection = $collection;
        }

        return $this->collection;
    }
    /**
     * @return bool
     */
    public function isRatingEnabled()
    {
        return $this->config->isRatingEnabled();
    }

    /**
     * @param null $type
     *
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'kb.default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'kb.default');
        }

        return;
    }

    /**
     * @return \Magento\Framework\View\Element\RendererList
     */
    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'kb.details.renderers'
        );
    }

    /**
     * @return bool
     */
    public function isShowAuthor()
    {
        return !$this->config->isArticleHideAuthor();
    }

    /**
     * @return bool
     */
    public function isShowDate()
    {
        return !$this->config->isArticleHideDate();
    }
}
