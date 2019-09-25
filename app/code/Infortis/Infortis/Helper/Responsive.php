<?php

namespace Infortis\Infortis\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Responsive extends AbstractHelper
{   
    /**
     * Map: breakpoint name to breakpoint value (width of viewport)
     * Important: these breakpoints correspond with similar variables in LESS files.
     *
     * @var array
     */
    protected $breakpoint = [
        '3XL' => 1920,
        '2XL' => 1680,
        'XL'  => 1440,
        'L'   => 1200,
        'M'   => 992,
        'S'   => 768,
        'XS'  => 640,
        '2XS' => 480,
        '3XS' => 320,
    ];

    /**
     * Map: breakpoint value to actual page width
     *
     * @var array
     */
    protected $pageWidth = [
        1920 => 1740,     // 3XL
        1680 => 1500,     // 2XL
        1440 => 1380,     //  XL
        1200 => 1170,     //   L
        992  => 970,      //   M
        768  => 750,      //   S
        640  => 600,      //  XS
        480  => 440,      // 2XS
        320  => 300,      // 3XS
    ];


    /**
     * Get maximum breakpoint based on maximum page width
     *
     * @param int Maximum page width
     * @return int
     */
    public function mapWidthToBreakpoint($width)
    {
        $breakpoint = $this->breakpoint;

        // If full width was selected, return max breakpoint
        if ($width === 0)
        {
            return $breakpoint['3XL'];
        }

        // Otherwise return breakpoint based on maximum page width
        if ($width < $breakpoint['M'])
        {
            $maxBreak = $breakpoint['S'];
        }
        elseif ($width < $breakpoint['L'])
        {
            $maxBreak = $breakpoint['M'];
        }
        elseif ($width < $breakpoint['XL'])
        {
            $maxBreak = $breakpoint['L'];
        }
        elseif ($width < $breakpoint['2XL'])
        {
            $maxBreak = $breakpoint['XL'];
        }
        elseif ($width < $breakpoint['3XL'])
        {
            $maxBreak = $breakpoint['2XL'];
        }
        else
        {
            $maxBreak = $breakpoint['3XL'];
        }
        
        return $maxBreak;
    }

    /**
     * Get array: map breakpoint name to breakpoint
     *
     * @return array
     */
    public function getMapBreakpointNameToBreakpoint()
    {
        return $this->breakpoint;
    }

    /**
     * Get array: map breakpoint to actual page width
     *
     * @return array
     */
    public function getMapBreakpointToPageWidth()
    {
        return $this->pageWidth;
    }
}
