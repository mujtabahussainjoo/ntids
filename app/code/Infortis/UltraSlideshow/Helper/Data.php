<?php

namespace Infortis\UltraSlideshow\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION = 'ultraslideshow';

    /**
     * Get settings
     *
     * @return string
     */
    public function getCfg($optionString, $storeCode = NULL)
    {
        return $this->scopeConfig->getValue(self::CONFIG_SECTION . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Get slideshow position
     *
     * @return string
     */
    /*
    public function getPosition()
    {
        return $this->getCfg('general/position');
    }
    */
}
