<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Rbslider\Api\Data\StatisticInterface;
use Aheadworks\Rbslider\Api\Data\StatisticExtensionInterface;

/**
 * Statistic data model
 * @codeCoverageIgnore
 */
class Statistic extends AbstractExtensibleObject implements StatisticInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlideBannerId()
    {
        return $this->_get(self::SLIDE_BANNER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlideBannerId($slideBannerId)
    {
        return $this->setData(self::SLIDE_BANNER_ID, $slideBannerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewCount()
    {
        return $this->_get(self::VIEW_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setViewCount($viewCount)
    {
        return $this->setData(self::VIEW_COUNT, $viewCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getClickCount()
    {
        return $this->_get(self::CLICK_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setClickCount($clickCount)
    {
        return $this->setData(self::CLICK_COUNT, $clickCount);
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
    public function setExtensionAttributes(StatisticExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
