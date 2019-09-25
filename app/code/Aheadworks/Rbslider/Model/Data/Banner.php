<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\Data\BannerExtensionInterface;

/**
 * Banner data model
 * @codeCoverageIgnore
 */
class Banner extends AbstractExtensibleObject implements BannerInterface
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
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageType()
    {
        return $this->_get(self::PAGE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageType($pageType)
    {
        return $this->setData(self::PAGE_TYPE, $pageType);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductCondition()
    {
        return $this->_get(self::PRODUCT_CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductCondition($productCondition)
    {
        return $this->setData(self::PRODUCT_CONDITION, $productCondition);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->_get(self::CATEGORY_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($categoryIds)
    {
        return $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getAnimationEffect()
    {
        return $this->_get(self::ANIMATION_EFFECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAnimationEffect($animationEffect)
    {
        return $this->setData(self::ANIMATION_EFFECT, $animationEffect);
    }

    /**
     * {@inheritdoc}
     */
    public function getPauseTimeBetweenTransitions()
    {
        return $this->_get(self::PAUSE_TIME_BETWEEN_TRANSITIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPauseTimeBetweenTransitions($pauseTimeBetweenTransitions)
    {
        return $this->setData(self::PAUSE_TIME_BETWEEN_TRANSITIONS, $pauseTimeBetweenTransitions);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlideTransitionSpeed()
    {
        return $this->_get(self::SLIDE_TRANSITION_SPEED);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlideTransitionSpeed($slideTransitionSpeed)
    {
        return $this->setData(self::SLIDE_TRANSITION_SPEED, $slideTransitionSpeed);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsStopAnimationMouseOnBanner()
    {
        return $this->_get(self::IS_STOP_ANIMATION_MOUSE_ON_BANNER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsStopAnimationMouseOnBanner($isStopAnimationMouseOnBanner)
    {
        return $this->setData(self::IS_STOP_ANIMATION_MOUSE_ON_BANNER, $isStopAnimationMouseOnBanner);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayArrows()
    {
        return $this->_get(self::DISPLAY_ARROWS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayArrows($displayArrows)
    {
        return $this->setData(self::DISPLAY_ARROWS, $displayArrows);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayBullets()
    {
        return $this->_get(self::DISPLAY_BULLETS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayBullets($displayBullets)
    {
        return $this->setData(self::DISPLAY_BULLETS, $displayBullets);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRandomOrderImage()
    {
        return $this->_get(self::IS_RANDOM_ORDER_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRandomOrderImage($isRandomOrderImage)
    {
        return $this->setData(self::IS_RANDOM_ORDER_IMAGE, $isRandomOrderImage);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlideIds()
    {
        return $this->_get(self::SLIDE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlideIds($slideIds)
    {
        return $this->setData(self::SLIDE_IDS, $slideIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlidePosition()
    {
        return $this->_get(self::SLIDE_POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlidePosition($slidePosition)
    {
        return $this->setData(self::SLIDE_POSITION, $slidePosition);
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
    public function setExtensionAttributes(BannerExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
