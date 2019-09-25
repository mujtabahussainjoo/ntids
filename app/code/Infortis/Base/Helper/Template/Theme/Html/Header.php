<?php
/**
 * Header block helper
 */

namespace Infortis\Base\Helper\Template\Theme\Html;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Header extends AbstractHelper
{
    /**
     * Main helper of the theme
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * Positions of header blocks
     *
     * @var array
     */
    protected $position;
    
    /**
     * Initialization
     */
    public function __construct(
        Context $context, 
        HelperData $helper
    ) {
        $this->theme = $helper;

        parent::__construct($context);

        $this->calculatePositions();
    }

    /**
     * Retrieve positions of header blocks
     */
    protected function calculatePositions()
    {
        $this->position['logo']             = $this->theme->getCfg('header/logo_position');
        $this->position['main-menu']        = $this->theme->getCfg('header/main_menu_position');
        $this->position['search']           = $this->theme->getCfg('header/search_position');
        $this->position['account-links']    = $this->theme->getCfg('header/account_links_position');
        $this->position['user-menu']        = $this->theme->getCfg('header/user_menu_position');
        $this->position['compare']          = $this->theme->getCfg('header/compare_position');
        $this->position['cart']             = $this->theme->getCfg('header/cart_position');
        $this->position['currency']         = $this->theme->getCfg('header/currency_switcher_position');
        $this->position['language']         = $this->theme->getCfg('header/lang_switcher_position');
    }

    /**
     * Get positions
     *
     * @return array
     */
    public function getPositions()
    {
        return $this->position;
    }

    /**
     * Create grid classes for header sections
     *
     * @return array
     */
    public function getGridClasses()
    {
        // Width (in grid units) of product page sections
        $primLeftColUnits       = $this->theme->getCfg('header/left_column');
        $primCentralColUnits    = $this->theme->getCfg('header/central_column');
        $primRightColUnits      = $this->theme->getCfg('header/right_column');

        // Grid classes
        $grid = [];
        $classPrefix = 'grid12-';

        if (!empty($primLeftColUnits) && trim($primLeftColUnits) !== '')
        {
            $grid['primLeftCol']        = $classPrefix . $primLeftColUnits;
        }

        if (!empty($primCentralColUnits) && trim($primCentralColUnits) !== '')
        {
            $grid['primCentralCol']     = $classPrefix . $primCentralColUnits;
        }

        if (!empty($primRightColUnits) && trim($primRightColUnits) !== '')
        {
            $grid['primRightCol']       = $classPrefix . $primRightColUnits;
        }

        return $grid;
    }

    /**
     * Check if main menu is displayed inisde a section (full-width section) at the bottom of the header
     *
     * @return bool
     */
    public function isMenuDisplayedInFullWidthContainer()
    {
        if ($this->position['main-menu'] === 'menuContainer')
        {
            return true;
        }
        return false;
    }

    /**
     * Get array of flags indicating if child blocks of the header (e.g. cart) are displayed inside main menu.
     * Important: can be used in modules connectors.
     *
     * @return array
     */
    public function getIsDisplayedInMenu()
    {
        $display['search']  = false;
        $display['cart']    = false;
        $display['compare'] = false;

        if ($this->position['search'] === 'mainMenu')
        {
            $display['search'] = true;
        }

        if ($this->position['cart'] === 'mainMenu')
        {
            $display['cart'] = true;
        }

        if ($this->position['compare'] === 'mainMenu')
        {
            $display['compare'] = true;
        }

        return $display;
    }

    /**
     * Get array of flags indicating if child blocks of the header (e.g. cart) are displayed inside user menu.
     *
     * @return array
     */
    public function getIsDisplayedInUserMenu()
    {
        $display['search']          = false;
        $display['cart']            = false;
        $display['compare']         = false;
        $display['account-links']   = false;

        if ($this->position['search'] === 'userMenu')
        {
            $display['search'] = true;
        }

        if ($this->position['cart'] === 'userMenu')
        {
            $display['cart'] = true;
        }

        if ($this->position['compare'] === 'userMenu')
        {
            $display['compare'] = true;
        }

        if ($this->position['account-links'] === 'userMenu')
        {
            $display['account-links'] = true;
        }

        return $display;
    }

    /**
     * Prepare classes for dropdowns
     *
     * @return string
     */
    public function getDropdownsClasses()
    {
        $blockClasses = '';

        if ($this->theme->getCfgDesign('dropdowns/heading_filled'))
        {
            $blockClasses .= ' filled-heading';
        }

        if ($this->theme->getCfgDesign('dropdowns/with_pointer'))
        {
            $blockClasses .= ' show-pointer';
        }

        if ($this->theme->getCfgDesign('dropdowns/border_color'))
        {
            $blockClasses .= ' show-border';
        }

        return $blockClasses;
    }
}
