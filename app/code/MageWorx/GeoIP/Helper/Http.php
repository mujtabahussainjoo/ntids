<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Helper;

/**
 * GeoIP HTTP helper
 */
class Http extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ICONV_CHARSET = 'UTF-8';
    
    /**
     * Get HTTP user agent
     *
     * @param bool $clean
     * @return string
     */
    public function getHttpUserAgent($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }
    
    /**
     * Return clean HTTP value
     *
     * @param string $var
     * @param bool $clean
     * @return bool
     */
    protected function _getHttpCleanValue($var, $clean = true)
    {
        $value = $this->_getRequest()->getServer($var, '');
        if ($clean) {
            $value = $this->cleanString($value);
        }

        return $value;
    }
    
    /**
     * Check string
     *
     * @param string $string
     * @return string
     */
    public function cleanString($string)
    {
        return '"libiconv"' == ICONV_IMPL ? iconv(self::ICONV_CHARSET, self::ICONV_CHARSET . '//IGNORE', $string) : $string;
    }
}
