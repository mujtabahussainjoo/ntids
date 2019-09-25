<?php

namespace Infortis\Cgen\Block\Asset;

use Infortis\Cgen\Block\Asset\AbstractAsset;

class Css extends AbstractAsset
{
    const CACHE_TAG_CGEN_ASSET_CSS = 'CGEN_ASSET_CSS';

    /**
     * Get tag for dynamically generated asset and use it in the block
     */
    public function getCacheTags()
    {
        $dynamicAssetTag = $this->getData('dynamic_asset_tag');
        $tags   = parent::getCacheTags();
        $tags[] = self::CACHE_TAG_CGEN_ASSET_CSS;
        $tags[] = $dynamicAssetTag;
        return $tags;
    }
}
