<?php

namespace Infortis\Cgen\Block\AssetWrapper;

use Infortis\Cgen\Block\AssetWrapper\AbstractWrapper;
use Magento\Framework\View\Element\Template\Context;

class Multi extends AbstractWrapper
{
    const CGEN_ASSET_WRAPPER_MULTI_URL_BASE = 'cgen/dynamic/single';

    /**
     * @var \Infortis\Cgen\Helper\Definitions
     */
    protected $configHelper;

    public function __construct(
        Context $context,
        \Infortis\Cgen\Helper\Definitions $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get URLs of the assets
     *
     * @return string
     */
    public function getAllUrls()
    {
        $urls = [];
        $moduleShort = $this->getData('asset_id'); //$this->getAssetId();
        $moduleName = $this->configHelper->getModuleName($moduleShort);
        $moduleAssets = $this->configHelper->getModuleAssets($moduleName);
        foreach ($moduleAssets as $id => $assetInfo)
        {
            $urls[] = $this->getUrl(self::CGEN_ASSET_WRAPPER_MULTI_URL_BASE, ['m' => $moduleShort, 'f' => $id, '_secure' => $this->getRequest()->isSecure()]);
        }

        return $urls;
    }
}
