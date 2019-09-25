<?php

namespace Infortis\Cgen\Observer;

abstract class AbstractObserver
{
    /**
     * @var \Infortis\Cgen\Helper\AssetCache
     */
    protected $_dynamicAssetCacheHelper;
    
    public function __construct(
        \Infortis\Cgen\Helper\AssetCache $assetCacheHelper
    ) {
        $this->_dynamicAssetCacheHelper = $assetCacheHelper;
    }
    
    protected function cleanDynamicCssCache()
    {
        return $this->_dynamicAssetCacheHelper->cleanCssCache();
    }

    public function cleanDynamicCacheByTag($tags)
    {
        return $this->_dynamicAssetCacheHelper->cleanCacheByTag($tags);
    }
}
