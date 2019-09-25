<?php

namespace Infortis\UltraMegamenu\Helper;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Section name of module configuration
     */
    const CONFIG_SECTION = 'ultramegamenu';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * TODO: remove redundant
     * @var UrlInterface
     */
    //protected $urlInterface;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        Context $context,
        // UrlInterface $urlInterface,
        Registry $registry
    ) {
        //$this->urlInterface = $urlInterface;
        //$this->urlInterface = $context->getUrlBuilder();
        $this->request = $context->getRequest();
        $this->scopeConfigInterface = $context->getScopeConfig();
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * Get configuration
     *
     * @var string
     */
    public function getCfg($optionString)
    {
        return $this->scopeConfigInterface->getValue(self::CONFIG_SECTION . '/' . $optionString, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get mobile menu threshold if mobile mode enabled. Otherwise, return NULL.
     * Important: can be used in modules connectors.
     *
     * @var string/NULL
     */
    public function getMobileMenuThreshold()
    {
        if ($this->getCfg('general/mode') > 0) //Mobile mode not enabled
        {
            return NULL; //If no mobile menu, value of the threshold doesn't matter, so return NULL
        }
        else
        {
            return $this->getCfg('mobilemenu/threshold');
        }
    }

    /**
     * Get CSS class
     *
     * @return string
     */
    public function getBlocksVisibilityClassOnMobile()
    {
        // Special class to show items with category blocks but without subcategories
        $showItemsOnlyBlocksClass = ($this->getCfg('mobilemenu/show_items_only_blocks')) ? ' opt-sob' : '';

        // Class indicating to hide category blocks below predefined breakpoint
        $hideBlocksBelowClass = ($this->getCfg('mobilemenu/hide_blocks_below')) ? ' opt-hide480' : '';

        // Class that shows/hides category blocks of selected levels
        return 'opt-sb' . $this->getCfg('mobilemenu/show_blocks') . $showItemsOnlyBlocksClass . $hideBlocksBelowClass;
    }

    /**
     * Check if current url is url for home page.
     * This is a copy from class Magento\Theme\Block\Html\Header\Logo
     *
     * @return bool
     */
    public function isHomePage()
    {
        //return $this->urlInterface->getUrl('', ['_current' => true]) == $this->urlInterface->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        
        return $this->_urlBuilder->getUrl('', ['_current' => true]) == $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
    }

    /**
     * @deprecated This wrapper method was left for backward compatibility
     *
     * @return bool
     */
    public function getIsOnHome()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/infortis.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Deprecated method \Infortis\UltraMegamenu\Helper\Data::getIsOnHome() was used.');

        return $this->isHomePage();
    }

    /**
     * @deprecated
     */
    public function getIsHomePage()
    {
        return $this->isHomePage();
    }

    /**
     * Check if sidebar menu can be the main menu
     *
     * @var bool
     */
    public function isSidebarMenuMainMenu($sidebarIsMainMenu = NULL)
    {
        if ($sidebarIsMainMenu === NULL) //Param not set
        {
            $sidebarIsMainMenu = $this->getCfg('sidemenu/is_main');
        }

        if ($sidebarIsMainMenu)
        {
            //The sidebar menu was explicitly marked as the main menu
            return true;
        }
        else
        {
            //Check if the top menu exists
            //$fromReg = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Registry')->registry('umm_top_menu_exists');
            $fromReg = $this->registry->registry('umm_top_menu_exists');

            if ($fromReg)
            {
                return false;
            }
            else
            {
                //If the top menu doesn't exist, mark the sidebar menu as the main menu
                return true;
            }
        }
    }

    /**
     * Get container
     *
     * @return string
     */
    public function getOutermostContainer()
    {
        $result = "undefined";
        $value = $this->getCfg('mainmenu/outermost_container');

        if ($value === 'window')
        {
            $result = "'window'"; //Important: single quotes required for JavaScript code
        }
        elseif ($value === 'menuBar')
        {
            $result = "undefined";
        }
        elseif ($value === 'headPrimInner')
        {
            $result = "jQuery('.hp-blocks-holder')"; //CSS class of the inner container inside the primary header
        }

        return $result;
    }

    /**
     * Get container
     *
     * @return string
     */
    public function getFullwidthDropdownContainer()
    {
        $result = "undefined";
        $value = $this->getCfg('mainmenu/fullwidth_dd_container');

        if ($value === 'window')
        {
            $result = "'window'"; //Important: single quotes required
        }
        elseif ($value === 'menuBar')
        {
            $result = "undefined";
        }
        elseif ($value === 'headPrimInner')
        {
            $result = "jQuery('.hp-blocks-holder')";
        }

        return $result;
    }

    /**
     * Returns name of template file if option to remove menu on home page is not enabled.
     * Usage:
     * <action method="setTemplate">
     * <argument name="template" xsi:type="helper" helper="Infortis\UltraMegamenu\Helper\Data::getTopMenuTemplateIfNotHome" />
     * </action>
     *
     * @return string
     */
    // public function getTopMenuTemplateIfNotHome()
    // {
    //     $templateFile = 'mainmenu.phtml';
    //     // Is home page and should the menu be removed on home page
    //     if ($this->isHomePage() && $this->getCfg('mainmenu/remove_on_home'))
    //     {
    //         $templateFile = '';
    //     }
    //     return $templateFile;
    // }
}
