<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Sample\Converter;

/**
 * Class Xml
 * Converts banner's parameters from XML files
 */
class Xml implements \Magento\Framework\Config\ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = [];
        if (!$source instanceof \DOMDocument) {
            return $output;
        }

        $banners = $source->getElementsByTagName('banner');
        foreach ($banners as $banner) {
            $bannerData = [];
            /** @var $banner \DOMElement */
            foreach ($banner->childNodes as $child) {
                if (!$child instanceof \DOMElement) {
                    continue;
                }
                /** @var $banner \DOMElement */
                $bannerData[$child->nodeName] = $child->nodeValue;
            }
            $output[] = $bannerData;
        }
        return $output;
    }
}
