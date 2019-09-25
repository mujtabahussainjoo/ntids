<?php

namespace Infortis\Base\Helper;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Psr\Log\LoggerInterface;

class Fonts extends AbstractHelper
{
    /**
     * Custom font directory path, relative to: app/design/frontend/Infortis/<theme_name>/web/
     */
    const CUSTOM_FONTS_DIR = 'fonts/custom/';

    /**
     * Quotation mark for complex font family names (e.g. containing whitespace)
     */
    const QUOTE = "'";

    /**
     * Main helper of the theme
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * Asset repository
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * Supported file extensions
     *
     * @var array
     */
    protected $supportedExtensions = ['eot', 'woff2', 'woff', 'ttf', 'otf', 'svg'];
    
    public function __construct(
        Context $context,
        Repository $assetRepo,
        HelperData $helper
    ) {
        $this->assetRepo = $assetRepo;
        $this->theme = $helper;
        $this->_logger = $context->getLogger();

        parent::__construct($context);
    }

    /**
     * Get raw URL string with google fonts defined by user
     *
     * @return string
     */
    protected function getGoogleFontsString()
    {
        // Clear the string: remove whitespaces and unneeded quote characters
        $string = trim($this->theme->getCfgDesign('defined_fonts/google_fonts_string'));
        $string = trim($string, "'\"");

        return $string;
    }

    /**
     * Get raw string with names of custom local fonts defined by user
     *
     * @return string
     */
    protected function getLocalFontsString()
    {
        // Clear the string: remove whitespaces
        return trim($this->theme->getCfgDesign('defined_fonts/local_fonts_string'));
    }

    /**
     * Get raw string with font stacks defined by user
     *
     * @return string
     */
    protected function getFontStacksString()
    {
        // Clear the string: remove whitespaces
        return trim($this->theme->getCfgDesign('defined_fonts/font_stacks_string'));
    }

    /**
     * Get fallback font stack for body font
     *
     * @return string
     */
    public function getFallbackFontStack()
    {
        // Clear the string: remove whitespaces
        return trim($this->theme->getCfgDesign('font/fallback_body_font_stack'));
    }

    /**
     * Get final URL string with google fonts to load
     *
     * @return string
     */
    public function getGoogleFontsFinalUrl()
    {
        $url = $this->getGoogleFontsString();

        // Remove https protocol name
        $count = 0;
        $pattern = "https:";
        $url = preg_replace('/^' . preg_quote($pattern) . '/', '', $url, -1, $count);

        // If protocol name not found, search for http
        if ($count === 0)
        {
            $pattern2 = "http:";
            $url = preg_replace('/^' . preg_quote($pattern2) . '/', '', $url, -1);
        }

        return $url;
    }

    /**
     * Get array with custom local fonts defined by user.
     * Array contains values for the "font-face" CSS at-rule:
     * - 'font-family': font family name
     * - 'src': resource containing the font data
     *
     * @return array
     */
    public function getLocalFontsFontface()
    {
        $fontfaceProperties = [];

        $fontNames = $this->getLocalFontsFamilies(); // Get font names with file extensions
        if (empty($fontNames))
        {
            return $fontfaceProperties;
        }

        foreach ($fontNames as $name)
        {
            // Important: don't remove file extensions before calling generateFontfaceSource(...)
            $src = $this->generateFontfaceSource($name);

            // Prepare font name: trim quotes (before removing extension), remove extension, add quotes if needed
            $name = trim($name, "'\"");
            $name = $this->removeFileExtension($name);
            $name = $this->quoteIfNeeded($name);

            if ($src)
            {
                $fontfaceProperties[] = ['font-family' => $name, 'src' => $src];
            }
        }

        return $fontfaceProperties;
    }

    /**
     * Generate "src" property for "font-face" CSS at-rule which specifies
     * the resource containing the font data.
     *
     * @param string $fontName
     * @return string
     */
    protected function generateFontfaceSource($fontName)
    {
        // Trim quotes
        $fontName = trim($fontName, "'\"");

        // Check if the font name contains a file extension
        $origExt = pathinfo($fontName, PATHINFO_EXTENSION);
        if ($origExt)
        {
            $fontName = pathinfo($fontName, PATHINFO_FILENAME);
            $extensions = [strtolower($origExt)];
        }
        else
        {
            $extensions = $this->supportedExtensions;
        }

        // Loop through all extensions and build "src" property
        $result = "src:";
        $resultForEotType = '';
        $i = 0;
        foreach ($extensions as $ext)
        {
            // Asset path
            $fontPath = self::CUSTOM_FONTS_DIR . $this->convertFontNameToFileName($fontName) . '.' . $ext;

            // Try to create asset
            try
            {
                $asset = $this->assetRepo->createAsset($fontPath);
                $fontUrl = $asset->getUrl();
                $sourceAsset = $asset->getSourceFile();
            }
            catch(\Exception $e)
            {
                // The source file does not exist so the asset also does not exist
                $fontUrl = null;
            }

            // Check if asset exists
            if ($fontUrl)
            {
                if ($ext === 'eot')
                {
                    $resultForEotType = "src: url('{$fontUrl}');";
                    $result .= " url('{$fontUrl}') format('embedded-opentype'),";
                }
                elseif ($ext === 'woff2')
                {
                    $result .= " url('{$fontUrl}') format('woff2'),";
                }
                elseif ($ext === 'woff')
                {
                    $result .= " url('{$fontUrl}') format('woff'),";
                }
                elseif ($ext === 'ttf')
                {
                    $result .= " url('{$fontUrl}') format('truetype'),";
                }
                elseif ($ext === 'otf')
                {
                    $result .= " url('{$fontUrl}') format('opentype'),";
                }
                elseif ($ext === 'svg')
                {
                    $result .= " url('{$fontUrl}') format('svg'),";
                }

                $i++;
            }
        }

        // If at least one asset exists
        if ($i)
        {
            $result = rtrim($result, ', '); // Remove the last occurrence of the comma
            $result .= ";\n"; // Add final semicolon

            if ($resultForEotType)
            {
                $result = $resultForEotType . "\n" . $result;
            }
        }
        else
        {
            // If no asset exists, erase the initial value of $result
            $result = '';

            $this->_logger->info(
                "No font file available for font {$fontName}."
                . "Make sure to upload font file to proper folder, set correct file permissions, deploy static files and flush the cache."
            );
        }

        return $result;
    }

    /**
     * Get names of Google fonts defined by user
     *
     * @return array
     */
    protected function getGoogleFontsFamilies()
    {
        $finalFontNames = [];

        // Get URL string with defined fonts. The string can look like in this example:
        // https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,700|PT+Sans+Narrow|Roboto:300,300i,400&amp;subset=greek,latin-ext
        $fontStringRaw = $this->getGoogleFontsString();

        // Get string starting from 'family=' and remove that start
        $fontStringRaw = strstr($fontStringRaw, 'family=');
        $fontStringRaw = str_replace('family=', '', $fontStringRaw);

        // If string contains '&'
        if (strpos($fontStringRaw, '&') !== false)
        {
            // Get definition of font families, omit definition of script subsets.
            // Retrieve the part of the string before the first occurrence of '&'.
            $fontStringRaw = strstr($fontStringRaw, '&', true);
        }

        // Get array of font definitions (strings) from the string
        // At this stage, the string looks like this: Open+Sans:400,400i,600,700|PT+Sans+Narrow|Roboto:300,300i,400
        $fontStringsArray = explode('|', $fontStringRaw);

        // Create list of font names
        foreach ($fontStringsArray as $fontString)
        {
            $exploded = explode(':', $fontString);

            // After the explode, $exploded[0] contains font name, $exploded[1] contains font styles and weights.
            // Convert font name to readable version, for example: Open+Sans => Open Sans.
            $rawFontName = $exploded[0];
            $count = 0;
            $fontName = str_replace('+', ' ', $rawFontName, $count);

            // If the number of replacements performed is greater than zero, font name will be quoted
            if ($count > 0)
            {
                $fontName = self::QUOTE . $fontName . self::QUOTE;
            }

            // Font string not always has definition of styles and weights, so the array not always has element [1].
            // If it has, list the styles.
            if (isset($exploded[1]))
            {
                $fontName = $fontName . '  (available: ';
                foreach (explode(',', $exploded[1]) as $style)
                {
                    // If style is italic
                    $end = strstr($style, 'i');
                    if ($end === 'i' || $end === 'italic')
                    {
                        $style = str_replace($end, '', $style);
                        $fontName .= $style . ' italic' . ', ';
                    }
                    else
                    {
                        $fontName .= $style . ', ';
                    }
                }
                $fontName = rtrim($fontName, ', '); // Remove the last occurrence of the comma
                $fontName .= ')';
            }
            // else
            // {
            //     $fontName = $fontName . '  (available: 400)';
            // }

            $finalFontNames[] = $fontName;
        }

        return $finalFontNames;
    }

    /**
     * Get names of custom local fonts defined by user
     *
     * @param bool $removeExtension
     * @return array
     */
    protected function getLocalFontsFamilies($removeExtension = false)
    {
        $finalFontNames = [];

        // Get string with names defined by user. The string can look like in this example:
        // Ubuntu-Medium; Montserrat; Sun Valley; My Font.svg; 'Weston Free'
        $fontStringRaw = $this->getLocalFontsString();
        if (empty($fontStringRaw))
        {
            return $finalFontNames;
        }

        // Create list of font names
        foreach (explode(';', $fontStringRaw) as $fontName)
        {
            // Trim whitespaces
            $fontName = trim($fontName);

            // Trim quotes added by user
            $fontName = trim($fontName, "'\"");

            // Check if font name contains a file extension and remove it
            if ($removeExtension)
            {
                $fontName = $this->removeFileExtension($fontName);
            }

            // Add quotes if needed
            $fontName = $this->quoteIfNeeded($fontName);

            $finalFontNames[] = $fontName;
        }

        return $finalFontNames;
    }

    /**
     * Get font stacks defined by user
     *
     * @return array
     */
    protected function getCustomFontStacks()
    {
        $finalFontNames = [];

        // Get string with font stacks defined by user
        $fontStringRaw = $this->getFontStacksString();
        if (empty($fontStringRaw))
        {
            return $finalFontNames;
        }

        // Create list of font names
        foreach (explode(';', $fontStringRaw) as $fontName)
        {
            // Trim whitespaces and quotes
            $fontName = trim($fontName);
            $finalFontNames[] = $fontName;
        }

        return $finalFontNames;
    }

    /**
     * Remove file extension from font name
     *
     * @param string $fontName
     * @return string
     */
    protected function removeFileExtension($fontName)
    {
        // Remove extension
        $origExt = pathinfo($fontName, PATHINFO_EXTENSION);
        if ($origExt)
        {
            $fontName = pathinfo($fontName, PATHINFO_FILENAME);
        }

        return $fontName;
    }

    /**
     * Add quotes to font name
     *
     * @param string $fontName
     * @return string
     */
    protected function quoteIfNeeded($fontName)
    {
        // If font name contains space, it will be quoted
        if (strpos($fontName, ' ') !== false)
        {
            $fontName = self::QUOTE . $fontName . self::QUOTE;
        }

        // If font name contains hyphen, it will be quoted
        if (strpos($fontName, '-') !== false)
        {
            $fontName = self::QUOTE . $fontName . self::QUOTE;
        }

        return $fontName;
    }

    /**
     * Convert font name to file name
     *
     * @param string $fontName
     * @return string
     */
    protected function convertFontNameToFileName($fontName)
    {
        // Trim whitespaces and quotes, replace spaces with hyphen
        $fontName = trim($fontName);
        $fontName = trim($fontName, "'\"");
        $fontName = str_replace(' ', '-', $fontName);

        return $fontName;
    }

    /**
     * Remove additional info in parentheses
     *
     * @return string
     */
    public function removeAdditionalFontInfo($name)
    {
        // Remove text in parentheses
        $name = preg_replace('/\(.*\)/', '', $name);
        return trim($name);
    }

    /**
     * Get names of fonts defined by user
     *
     * @return array
     */
    public function getDefinedFontFamilies()
    {
        $finalFontNames = [];

        $googleFontsNames = $this->getGoogleFontsFamilies();
        $localFontsNames = $this->getLocalFontsFamilies(true); // Remove file extensions from local font names
        $fontStacks = $this->getCustomFontStacks();
        $finalFontNames = array_merge($googleFontsNames, $localFontsNames, $fontStacks);

        return $finalFontNames;
    }
}
