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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getBaseUrl($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/base_url', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return bool
     */
    public function isRatingEnabled($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/is_rating_enabled', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return bool
     */
    public function isUrlRewriteEnabled($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/is_url_rewrite_enabled', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return bool
     */
    public function isArticleHideAuthor($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/article_hide_author', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return bool
     */
    public function isArticleHideDate($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/article_hide_date', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getArticleLinksLimit($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/article_links_limit', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getCategoryArticleAmount($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/category_article_amount', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getCategoryURLExcluded($store = null)
    {
        return $this->scopeConfig->getValue('kb/general/category_url_exclude', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getCommentProvider($store = null)
    {
        return $this->scopeConfig->getValue('kb/comments/provider', ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getDisqusShortname($store = null)
    {
        return $this->scopeConfig->getValue('kb/comments/disqus_shortname', ScopeInterface::SCOPE_STORE, $store);
    }
}
