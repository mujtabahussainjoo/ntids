<?php

namespace Infortis\Cgen\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Definitions extends AbstractHelper
{
    /**
     * @var array
     */
    protected $_moduleName = [
        'iult' => 'Infortis_Ultimo',
    ];

    /**
     * @var array
     */
    protected $_moduleAssetsArray = [
        'Infortis_Ultimo' => [
            'design.css' => [
                'cache_tag' => 'THEME_DESIGN',
                'template'  => 'assets/css/design.phtml',
            ],
            // 'grid.css' => [
            //     'cache_tag' => 'THEME_GRID',
            //     'template'  => 'assets/css/grid.phtml',
            // ],
            'layout.css' => [
                'cache_tag' => 'THEME_LAYOUT',
                'template'  => 'assets/css/layout.phtml',
            ],
        ],
    ];

    /**
     * @var array
     */
    protected $_sectionTags = [
        'theme_settings' => [
            'THEME_LAYOUT',
            \Infortis\Cgen\Block\Asset\Css::CACHE_TAG_CGEN_ASSET_CSS,
        ],
        'theme_design' => [
            'THEME_DESIGN',
            \Infortis\Cgen\Block\Asset\Css::CACHE_TAG_CGEN_ASSET_CSS,
        ],
        'theme_layout' => [
            'THEME_LAYOUT',
            \Infortis\Cgen\Block\Asset\Css::CACHE_TAG_CGEN_ASSET_CSS,
        ],
    ];

    public function getModuleName($id)
    {
        return $this->_moduleName[$id];
    }

    public function getModuleAssets($moduleName)
    {
        return $this->_moduleAssetsArray[$moduleName];
    }

    public function getModuleAsset($moduleName, $assetId)
    {
        return $this->_moduleAssetsArray[$moduleName][$assetId];
    }

    public function getSectionTags($sectionId)
    {
        return $this->_sectionTags[$sectionId];
    }
}
