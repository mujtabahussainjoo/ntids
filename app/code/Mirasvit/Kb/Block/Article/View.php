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

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Mirasvit\Kb\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Mirasvit\Kb\Model\Config $config
     * @param \Mirasvit\Kb\Helper\Data $kbData
     * @param \Mirasvit\Kb\Helper\Vote $kbVote
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Mirasvit\Kb\Helper\Vote $kbVote,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->config = $config;
        $this->kbData = $kbData;
        $this->kbVote = $kbVote;
        $this->catalogData = $catalogData;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $article = $this->getArticle();
        if (!$article) {
            return;
        }
        $category = $article->getCategory();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($article->getMetaTitle() ? $article->getMetaTitle() : $article->getName());
            $headBlock->setDescription($article->getMetaDescription());
            $headBlock->setKeywords($article->getMetaKeywords());
        }

        $metaTitle = $article->getMetaTitle();
        if (!$metaTitle) {
            $metaTitle = $article->getName();
        }

        $metaDescription = $article->getMetaDescription();
        if (!$metaDescription) {
            $metaDescription = $this->filterManager->truncate(
                $this->filterManager->stripTags($article->getText()),
                ['length' => 150, 'etc' => ' ...', 'remainder' => '', 'breakWords' => false]
            );
        }
        $this->pageConfig->getTitle()->set($metaTitle);
        $this->pageConfig->setDescription($article->getName() . ' ' . $metaDescription);
        $this->pageConfig->setKeywords($article->getMetaKeywords());

        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
            ]);
            $ids = [0];
            if (is_array($category->getParentIds())) {
                $ids = array_merge($ids, $category->getParentIds());
            }
            if (in_array(1, $ids)) {
                unset($ids[array_search(1, $ids)]);
            }
            $ids[] = 0;
            $parents = $this->categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', $ids)
                ->setOrder('level', 'asc');
            foreach ($parents as $cat) {
                $breadcrumbs->addCrumb('kbase' . $cat->getUrlKey(), [
                    'label' => $cat->getName(),
                    'title' => $cat->getName(),
                    'link'  => $cat->getUrl(),
                ]);
            }
            $breadcrumbs->addCrumb('kbase' . $category->getUrlKey(), [
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link'  => $category->getUrl(),
            ]);
            $breadcrumbs->addCrumb('kbase' . $article->getUrlKey(), [
                'label' => $article->getName(),
                'title' => $article->getName(),
            ]);
        }

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($article->getName());
        }
    }

    /**
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getKbPageTitle()
    {
        return $this->getLayout()->getBlock('page.main.title')->toHtml();
    }

    /**
     * @return null|\Mirasvit\Kb\Model\Article
     */
    public function getArticle()
    {
        return $this->registry->registry('current_article');
    }

    /**
     * @return \Mirasvit\Kb\Model\ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        $collection = $this->getArticle()->getCategories()
            ->addFieldToFilter('is_active', true);

        return $collection;
    }

    /**
     * @return \Mirasvit\Kb\Model\ResourceModel\Tag\Collection
     */
    public function getTags()
    {
        $collection = $this->getArticle()->getTags();

        return $collection;
    }

    /**
     * @param int $vote
     *
     * @return string
     */
    public function getVoteUrl($vote)
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/vote', [
            'id'   => $this->getArticle()->getId(),
            'vote' => $vote,
        ]);
    }

    /**
     * @return int|void
     */
    public function getVoteResult()
    {
        return $this->kbVote->getVoteResult($this->getArticle());
    }

    /**
     * @return bool|null|string
     */
    public function isRatingEnabled()
    {
        return $this->config->isRatingEnabled();
    }

    /**
     *
     * @param \Mirasvit\Kb\Model\Article $article
     *
     * @return string
     *
     * @throws \Exception
     * @deprecated
     */
    public function getArticleText($article)
    {
        $helper = $this->catalogData;
        $processor = $helper->getPageTemplateProcessor();
        $html = $processor->filter($article->getText());

        return $html;
    }

    /**
     * @return string
     */
    public function getCommentProvider()
    {
        return $this->config->getCommentProvider();
    }

    /**
     * @return string
     */
    public function getDisqusShortname()
    {
        return $this->config->getDisqusShortname();
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

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        if ($this->getArticle()) {
            return parent::toHtml();
        }
    }
}
