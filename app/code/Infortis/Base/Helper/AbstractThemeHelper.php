<?php

namespace Infortis\Base\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

abstract class AbstractThemeHelper extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION_SETTINGS   = 'theme_settings';
    const CONFIG_SECTION_DESIGN     = 'theme_design';
    const CONFIG_SECTION_LAYOUT     = 'theme_layout';

    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;
    
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->_configScopeConfigInterface = $context->getScopeConfig();
        parent::__construct($context);
    }
    
    /**
     * Get theme's main configuration (single option)
     *
     * @return string
     */
    public function getCfg($optionString, $storeCode = NULL)
    {
        return $this->_configScopeConfigInterface->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }
    
    /**
     * Get theme's design configuration (single option)
     *
     * @return string
     */
    public function getCfgDesign($optionString, $storeCode = NULL)
    {
        return $this->_configScopeConfigInterface->getValue(self::CONFIG_SECTION_DESIGN . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);
    }
    
    /**
     * Get theme's layout configuration (single option)
     *
     * @return string
     */
    public function getCfgLayout($optionString, $storeCode = NULL)
    {
        return $this->_configScopeConfigInterface->getValue(self::CONFIG_SECTION_LAYOUT . '/' . $optionString, ScopeInterface::SCOPE_STORE, $storeCode);

    }

    /**
     * Get selected section from the configuration
     *
     * @return array
     */
    public function getCfgSection($section, $storeCode = NULL)
    {
        return $this->_configScopeConfigInterface->getValue($section, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * Special method for assets generation.
     * Get selected section from the configuration: theme's design section
     *
     * @return array
     */
    public function getCfgSectionDesign($storeCode = NULL)
    {
        return $this->getCfgSection(self::CONFIG_SECTION_DESIGN, $storeCode);
    }

    /**
     * Special method.
     * Get selected group from theme's main configuration
     *
     * @return array
     */
    //TODO: new method name
    //public function getCfgGroupSettings($group, $storeCode = NULL)
    public function getCfgGroup($group, $storeCode = NULL)
    {
        //TODO: new code, uses existing method to simplify the code.
        //return $this->getCfgSection(self::CONFIG_SECTION_SETTINGS . '/' . $group, ScopeInterface::SCOPE_STORE, $storeCode);

        return $this->_configScopeConfigInterface->getValue(self::CONFIG_SECTION_SETTINGS . '/' . $group, ScopeInterface::SCOPE_STORE, $storeCode);
    }

}
