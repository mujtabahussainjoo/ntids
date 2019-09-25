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


namespace Mirasvit\Kb\Service;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sitemap implements \Mirasvit\Kb\Api\Data\SitemapInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Core\Api\UrlRewriteHelperInterface $urlRewrite,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Mirasvit\Kb\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->urlRewrite                = $urlRewrite;
        $this->kbData                    = $kbData;
        $this->config                    = $config;
        $this->articleCollectionFactory  = $articleCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager              = $storeManager;
        $this->state                     = $state;
        $this->session                   = $session;
        $this->sidResolver               = $sidResolver;
        $this->urlBuilder                = $urlBuilder;
        $this->objectManager             = $objectManager;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getBaseRoute($storeId = 0)
    {
        return $this->config->getBaseUrl($storeId);
    }

    /**
     * @param int $storeId
     * @param int $parentId
     * @return array
     */
    public function getCategoryTree($storeId = 0, $parentId = 0)
    {
        $list = [];

        if ($parentId == null) {
            $parentId = $this->kbData->getRootCategory($storeId)->getId();
        }

        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('parent_id', $parentId)
            ->addStoreIdFilter($storeId)
            ->setOrder('position', 'asc')
        ;

        foreach ($collection as $item) {
            $list[] = $item;
            if ($item->hasChildren()) {
                $children = $this->getCategoryTree($storeId, $item->getId());
                foreach ($children as $child) {
                    $list[] = $child;
                }
            }
        }

        return $list;
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getBlogItem($storeId = 0)
    {
        $this->urlRewrite->registerBasePath('KBASE', $this->config->getBaseUrl($storeId));
        $baseUrlCollection = new \Magento\Framework\DataObject(
            [
                'url' => $this->getBaseRoute($storeId),
            ]
        );

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => ['Homepage' => $baseUrlCollection],
            ]
        );

        return $sitemapItem;
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject|bool
     */
    public function getCategoryItems($storeId = 0)
    {
        $this->urlRewrite->registerBasePath('KBASE', $this->config->getBaseUrl($storeId));
        $categoryTree = $this->getCategoryTree($storeId);
        if (!$categoryTree) {
            return false;
        }

        foreach ($categoryTree as $category) {
            $categoryCollection[] = new \Magento\Framework\DataObject(
                [
                    'name' => $category->getName(),
                    'url' => $this->prepareSitemapUrl($category->getUrl()),
                ]
            );
        }

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => $categoryCollection,
            ]
        );

        return $sitemapItem;
    }

    /**
     * @param int $storeId
     * @return \Magento\Framework\DataObject|bool
     */
    public function getPostItems($storeId)
    {
        $this->urlRewrite->registerBasePath('KBASE', $this->config->getBaseUrl($storeId));
        $postCollectionFactory = $this->articleCollectionFactory->create()
            ->addStoreIdFilter($storeId)
            ->addVisibilityFilter();

        if ($postCollectionFactory->getSize() <= 0) {
            return false;
        }

        $postCollection = null;
        /** @var \Mirasvit\Kb\Model\Article $post */
        foreach ($postCollectionFactory as $post) {
            $postCollection[] = new \Magento\Framework\DataObject(
                [
                    'name' => $post->getName(),
                    'url' => $this->prepareSitemapUrl($post->getUrl()),
                ]
            );
        }

        $sitemapItem = new \Magento\Framework\DataObject(
            [
                'changefreq' => self::CHANGEFREQ,
                'priority' => self::PRIORITY,
                'collection' => $postCollection,
            ]
        );

        return $sitemapItem;
    }

    /**
     * @param string $url
     * @return string
     */
    private function prepareSitemapUrl($url)
    {
        $baseUrl = $this->urlBuilder->getBaseUrl();
        if (
            $this->storeManager->getStore()->getConfig(\Magento\Store\Model\Store::XML_PATH_STORE_IN_URL) &&
            $this->state->getAreaCode() != \Magento\Framework\App\Area::AREA_CRONTAB
        ) {
            $baseUrl .= $this->storeManager->getStore()->getCode() . '/';
        }
        $sessionKey = $this->sidResolver->getSessionIdQueryParam($this->session);

        $url = preg_replace('/\??'.$sessionKey.'=[^&]*/', '', $url);
        $url = str_replace($baseUrl, '', $url);

        return $url;
    }
}
