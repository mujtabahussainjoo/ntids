<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Api\Data;

/**
 * Banner interface
 * @api
 */
interface BannerInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const PAGE_TYPE = 'page_type';
    const POSITION = 'position';
    const PRODUCT_CONDITION = 'product_condition';
    const CATEGORY_IDS = 'category_ids';
    const ANIMATION_EFFECT = 'animation_effect';
    const PAUSE_TIME_BETWEEN_TRANSITIONS = 'pause_time_between_transitions';
    const SLIDE_TRANSITION_SPEED = 'slide_transition_speed';
    const IS_STOP_ANIMATION_MOUSE_ON_BANNER = 'is_stop_animation_mouse_on_banner';
    const DISPLAY_ARROWS = 'display_arrows';
    const DISPLAY_BULLETS = 'display_bullets';
    const IS_RANDOM_ORDER_IMAGE = 'is_random_order_image';
    const SLIDE_IDS = 'slide_ids';
    const SLIDE_POSITION = 'slide_position';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get page type
     *
     * @return int
     */
    public function getPageType();

    /**
     * Set page type
     *
     * @param int $pageType
     * @return $this
     */
    public function setPageType($pageType);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param int $position
     * @return $this
     */
    public function setPosition($position);

    /**
     * Get product condition
     *
     * @return \Aheadworks\Rbslider\Api\Data\ConditionInterface
     */
    public function getProductCondition();

    /**
     * Set product condition
     *
     * @param \Aheadworks\Rbslider\Api\Data\ConditionInterface $productCondition
     * @return $this
     */
    public function setProductCondition($productCondition);

    /**
     * Get category ids
     *
     * @return string
     */
    public function getCategoryIds();

    /**
     * Set category ids
     *
     * @param string $categoryIds
     * @return $this
     */
    public function setCategoryIds($categoryIds);

    /**
     * Get animation effect
     *
     * @return int
     */
    public function getAnimationEffect();

    /**
     * Set animation effect
     *
     * @param int $animationEffect
     * @return $this
     */
    public function setAnimationEffect($animationEffect);

    /**
     * Get pause time between transitions
     *
     * @return int
     */
    public function getPauseTimeBetweenTransitions();

    /**
     * Set pause time between transitions
     *
     * @param int $pauseTimeBetweenTransitions
     * @return $this
     */
    public function setPauseTimeBetweenTransitions($pauseTimeBetweenTransitions);

    /**
     * Get slide transition speed
     *
     * @return int
     */
    public function getSlideTransitionSpeed();

    /**
     * Set slide transition speed
     *
     * @param int $slideTransitionSpeed
     * @return $this
     */
    public function setSlideTransitionSpeed($slideTransitionSpeed);

    /**
     * Get is stop animation mouse on banner
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsStopAnimationMouseOnBanner();

    /**
     * Set is stop animation mouse on banner
     *
     * @param bool $isStopAnimationMouseOnBanner
     * @return $this
     */
    public function setIsStopAnimationMouseOnBanner($isStopAnimationMouseOnBanner);

    /**
     * Get display arrows
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisplayArrows();

    /**
     * Set display arrows
     *
     * @param bool $displayArrows
     * @return $this
     */
    public function setDisplayArrows($displayArrows);

    /**
     * Get display bullets
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisplayBullets();

    /**
     * Set display bullets
     *
     * @param bool $displayBullets
     * @return $this
     */
    public function setDisplayBullets($displayBullets);

    /**
     * Get is random order image
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRandomOrderImage();

    /**
     * Set is random order image
     *
     * @param bool $isRandomOrderImage
     * @return $this
     */
    public function setIsRandomOrderImage($isRandomOrderImage);

    /**
     * Get slide ids
     *
     * @return int[]
     */
    public function getSlideIds();

    /**
     * Set slide ids
     *
     * @param int[] $slideIds
     * @return $this
     */
    public function setSlideIds($slideIds);

    /**
     * Get slide position
     *
     * @return string
     */
    public function getSlidePosition();

    /**
     * Set slide position
     *
     * @param string $slidePosition
     * @return $this
     */
    public function setSlidePosition($slidePosition);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\Rbslider\Api\Data\BannerExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\Rbslider\Api\Data\BannerExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Aheadworks\Rbslider\Api\Data\BannerExtensionInterface $extensionAttributes);
}
