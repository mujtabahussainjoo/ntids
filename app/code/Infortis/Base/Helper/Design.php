<?php

namespace Infortis\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;

class Design extends AbstractHelper
{
    /**
     * The tail part of directory path for background image uploading
     */
    const BACKGROUND_DIR = 'wysiwyg/infortis/_bg/';

    /**
     * The tail part of directory path for textures
     */
    const PATTERN_DIR = 'images/tex/';

    /**
     * File extension
     */
    const PATTERN_FILE_EXT = '.png';

    /**
     * The tail part of directory path for flags
     */
    const FLAG_DIR = 'images/flags/';

    /**
     * File extension
     */
    const FLAG_FILE_EXT = '.png';

    /**
     * Asset service
     *
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;
    
    /**
     * Prepare paths
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->_assetRepo = $assetRepo;

        parent::__construct($context);
    }

    /**
     * Get URL of the background image
     *
     * @param string $image
     * @param array $params
     * @return string
     */
    public function getBackgroundUrl($image, array $params = [])
    {
        $path = self::BACKGROUND_DIR . $image;

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA], $params) . $path;
    }

    /**
     * Get URL of the pattern image
     *
     * @param string $pattern
     * @param array $params
     * @return string
     */
    public function getPatternUrl($pattern, array $params = [])
    {
        $path = self::PATTERN_DIR . $pattern . self::PATTERN_FILE_EXT;

        return $this->_assetRepo->getUrlWithParams($path, $params);
    }

    /**
     * Get URL of the flag image
     *
     * @param string $flag
     * @param array $params
     * @return string
     */
    public function getFlagUrl($flag, array $params = [])
    {
        $path = self::FLAG_DIR . $flag . self::FLAG_FILE_EXT;

        return $this->_assetRepo->getUrlWithParams($path, $params);
    }

}
