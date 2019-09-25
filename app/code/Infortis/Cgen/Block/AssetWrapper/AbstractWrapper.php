<?php

namespace Infortis\Cgen\Block\AssetWrapper;

abstract class AbstractWrapper extends \Magento\Framework\View\Element\Template
{
    const CACHE_TAG_CGEN_ASSET_WRAPPER = 'CGEN_ASSET_WRAPPER';

    protected function _construct()
    {
        $this->addData(
            [
                'cache_lifetime' => (3600 * 24 * 30),
            ]
        );
    }

    public function getCacheKeyInfo()
    {
        $base                   = parent::getCacheKeyInfo();
        $base['store_code']     = $this->_storeManager->getStore()->getCode();
        $base['website_code']   = $this->_storeManager->getWebsite()->getCode();
        $base['secure_url']     = $this->_storeManager->getStore()->isCurrentlySecure();
        return $base;
    }

    public function getCacheTags()
    {
        $tags = parent::getCacheTags();
        $tags[] = self::CACHE_TAG_CGEN_ASSET_WRAPPER;
        return $tags;
    }

    /**
     * Get store view code
     *
     * @return string
     */
    public function getStoreViewCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
