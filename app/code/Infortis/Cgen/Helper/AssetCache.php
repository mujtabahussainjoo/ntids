<?php

namespace Infortis\Cgen\Helper;

class AssetCache extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $cacheManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\CacheInterface $cacheManager        
    ) {
        $this->cacheManager = $cacheManager;
        parent::__construct($context);
    }

    /**
     * Clean cache for all CSS assets
     */
    public function cleanCssCache()
    {
        return $this->cacheManager->clean([\Infortis\Cgen\Block\Asset\Css::CACHE_TAG_CGEN_ASSET_CSS]);
    }

    /**
     * Clean cache for assets by tag
     */
    public function cleanCacheByTag($tags)
    {
        // // TODO
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // foreach ($tags as $tag)
        // {
        //     $logger->info('cleanCacheByTag(), tag=' . $tag); ///
        // }

        return $this->cacheManager->clean($tags);
    }
}
