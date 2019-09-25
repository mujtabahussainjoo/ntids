<?php

namespace Infortis\Base\Model\System\Config\Source\Design\Font\Family;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\Fonts as HelperFonts;

class DefinedFonts
{
    /**
     * Main helper of the theme
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * Fonts helper
     *
     * @var HelperFonts
     */
    protected $helperFonts;

    /**
     * Initialization
     */
    public function __construct(
        HelperData $helper,
        HelperFonts $helperFonts
    ) {
        $this->theme = $helper;
        $this->helperFonts = $helperFonts;
    }

    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => '', 'label' => __('-')];
        $fontNames = $this->helperFonts->getDefinedFontFamilies();

        foreach ($fontNames as $name)
        {
            // Remove additional info in parentheses
            $value = $this->helperFonts->removeAdditionalFontInfo($name);

            $options[] = ['value' => $value, 'label' => $name];
        }

        return $options;
    }
}
