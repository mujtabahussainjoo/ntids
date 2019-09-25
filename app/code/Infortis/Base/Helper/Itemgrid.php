<?php

namespace Infortis\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Itemgrid extends AbstractHelper
{
    /**
     * Values: number of columns / grid item width
     *
     * @var array
     */
    protected $_itemWidth = [
        "1" => 98,
        "2" => 48,
        "3" => 31.3333,
        "4" => 23,
        "5" => 18,
        "6" => 14.6666,
        "7" => 12.2857,
        "8" => 10.5,
    ];

    /**
     * Get CSS for item grid based on number of columns
     *
     * @param int $columnCount
     * @return string
     */
    public function getCssItemGrid($columnCount)
    {
        if ($columnCount < 1)
        {
            return '';
        }
        elseif ($columnCount > 8)
        {
            $columnCount = 8;
        }

        $out = "\n";
        $out .= '.itemgrid .item { width:' . $this->_itemWidth[$columnCount] . '%; clear:none !important; }' . "\n";
        
        if ($columnCount > 1)
        {
            $out .= '.itemgrid > li:nth-of-type(' . $columnCount . 'n+1) { clear:left !important; }' . "\n";
        }

        return $out;
    }

    /**
     * Get CSS to disable hover effect
     *
     * @return string
     */
    public function getCssDisableHoverEffect()
    {
        // Disable hover effect:
        // - Cancel "hover effect" styles: apply the same styles which item has without "hover effect"
        // - Show elements normally displayed only on hover
        // - Show full name even if enabled: display name in single line
        // - Spaces between items
        // TODO: removed:
        // .category-products-grid.hover-effect .item { border-top: none; }

        return 
        '
        /* Disable hover effect */
        .category-products-grid.hover-effect .item:hover {
            margin-left:0;
            margin-right:0;
            padding-left:1%;
            padding-right:1%;
            box-shadow: none !important;
            border-color: #f5f5f5;
        }
        .category-products-grid.hover-effect .item .display-onhover { display:block !important; }
        .category-products-grid.hover-effect.single-line-name .item .product-name { overflow: visible; white-space: normal; }
        ';

    }

    /**
     * Get CSS to disable hover effect
     *
     * @return string
     */
    public function getCssHideAddtoLinks()
    {
        // .category-products-grid.hover-effect .item .addto-links, <-- This rule is to override "display-onhover"

        return 
        '
        .category-products-grid.hover-effect .item .addto-links,
        .category-products-grid .item .addto-links {
            display: none !important;
        }
        ';

    }

    /**
     * @deprecated
     * Wrapper for new method, left for backward compatibility
     *
     * @param int $columnCount
     * @return string
     */
    public function getCssGridItem($columnCount)
    {
        return $this->getCssItemGrid($columnCount);
    }
}
