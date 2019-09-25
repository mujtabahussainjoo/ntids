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



namespace Mirasvit\Kb\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Tag extends AbstractHelper
{
    /**
     * @var \Mirasvit\Kb\Model\TagFactory
     */
    protected $tagFactory;

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Core\Api\UrlRewriteHelperInterface
     */
    protected $urlRewrite;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Mirasvit\Kb\Model\TagFactory                          $tagFactory
     * @param \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection              $resource
     * @param \Mirasvit\Core\Api\UrlRewriteHelperInterface           $urlRewrite
     * @param \Magento\Framework\App\Helper\Context                  $context
     * @param \Magento\Store\Model\StoreManagerInterface             $storeManager
     */
    public function __construct(
        \Mirasvit\Kb\Model\TagFactory $tagFactory,
        \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->tagFactory = $tagFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->resource = $resource;
        $this->urlRewrite = $urlRewrite;
        $this->context = $context;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param string                     $tags
     * @return void
     */
    public function setTags($article, $tags)
    {
        $tags = explode(',', $tags);

        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = $this->getTag($tagName);

            if (!$tag) {
                continue;
            }

            $tagIds[] = $tag->getId();
        }

        $article->setTagIds($tagIds);
    }

    /**
     * @param string $tagName
     *
     * @return \Mirasvit\Kb\Model\Tag
     */
    public function getTag($tagName)
    {
        $tagName = trim($tagName);
        if (!$tagName) {
            return false;
        }

        $collection = $this->tagCollectionFactory->create()
            ->addFieldToFilter('name', $tagName);

        if ($collection->count()) {
            $tag = $collection->getFirstItem();
        } else {
            $tag = $this->tagFactory->create()->setName($tagName)->save();
        }

        return $tag;
    }

    /**
     * @return string
     */
    public function getListUrl()
    {
        return $this->urlRewrite->getUrl('KBASE', 'TAG_LIST');
    }
}
