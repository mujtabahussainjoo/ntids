<?php

namespace Infortis\Cgen\Block\AssetWrapper;

use Infortis\Cgen\Block\AssetWrapper\AbstractWrapper;

class Combined extends AbstractWrapper
{
    const CGEN_ASSET_WRAPPER_COMBINED_URL_BASE = 'cgen/dynamic/assets'; //Note "s" at the end of controller name

    /**
     * Get URL of the asset
     *
     * @return string
     */
    //public function getAssetUrl($assetId, $assetName)
    public function getAssetUrl()
    {
        $assetId = $this->getData('asset_id'); //$this->getAssetId();
        $assetName = $this->getData('asset_name'); //$this->getAssetName();
        $url = $this->getUrl(self::CGEN_ASSET_WRAPPER_COMBINED_URL_BASE, ['m' => $assetId, 'f' => $assetName, '_secure' => $this->getRequest()->isSecure()]);
        return $url;
    }
}
