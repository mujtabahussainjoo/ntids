<?php

namespace Infortis\Cgen\Block\Asset;

abstract class AbstractAsset extends \Magento\Framework\View\Element\Template
{
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
        $base                 = parent::getCacheKeyInfo();
        $base['store_code']   = $this->_storeManager->getStore()->getCode();
        $base['website_code'] = $this->_storeManager->getWebsite()->getCode();                
        return $base;
    }

    /**
     * Get Store view code
     *
     * @return string
     */
    public function getStoreViewCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Returns true, if color code is specified and the value doesn't equal "transparent"
     *
     * @param string
     * @return bool
     */
    public function isColor($color)
    {
        if ($color && $color != 'transparent')
            return true;
        else
            return false;
    }
}
