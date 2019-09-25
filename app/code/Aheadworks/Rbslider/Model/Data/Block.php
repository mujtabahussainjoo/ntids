<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Data;

use Aheadworks\Rbslider\Api\Data\BlockInterface;
use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Rbslider\Api\Data\BlockExtensionInterface;

/**
 * Block data model
 * @codeCoverageIgnore
 */
class Block extends AbstractExtensibleObject implements BlockInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBanner()
    {
        return $this->_get(self::BANNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setBanner($banner)
    {
        return $this->setData(self::BANNER, $banner);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlides()
    {
        return $this->_get(self::SLIDES);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlides($slides)
    {
        return $this->setData(self::SLIDES, $slides);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(BlockExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
