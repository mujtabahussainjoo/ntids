<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model;

use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Api\Data\BannerExtensionInterface;
use Aheadworks\Rbslider\Model\ResourceModel\Banner as ResourceBanner;
use Aheadworks\Rbslider\Model\Rule\ProductFactory;
use Aheadworks\Rbslider\Model\Rule\Product;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Rbslider\Model\Converter\Condition as ConditionConverter;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class Banner
 * @package Aheadworks\Rbslider\Model
 */
class Banner extends AbstractModel implements BannerInterface
{
    /**
     * @var ProductFactory
     */
    private $productRuleFactory;

    /**
     * @var Product
     */
    private $productRule;

    /**
     * @var ConditionConverter
     */
    private $conditionConverter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProductFactory $productRuleFactory
     * @param ConditionConverter $conditionConverter
     * @param ResourceBanner|null $resource
     * @param ResourceBanner\Collection|null $resourceCollection
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProductFactory $productRuleFactory,
        ConditionConverter $conditionConverter,
        ResourceBanner $resource = null,
        ResourceBanner\Collection $resourceCollection = null
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection);
        $this->productRuleFactory = $productRuleFactory;
        $this->conditionConverter = $conditionConverter;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceBanner::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
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
        return $this->getData(self::NAME);
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
        return $this->getData(self::STATUS);
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
        return $this->getData(self::PAGE_TYPE);
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
        return $this->getData(self::POSITION);
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
        return $this->getData(self::PRODUCT_CONDITION);
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
        return $this->getData(self::CATEGORY_IDS);
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
        return $this->getData(self::ANIMATION_EFFECT);
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
        return $this->getData(self::PAUSE_TIME_BETWEEN_TRANSITIONS);
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
        return $this->getData(self::SLIDE_TRANSITION_SPEED);
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
        return $this->getData(self::IS_STOP_ANIMATION_MOUSE_ON_BANNER);
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
        return $this->getData(self::DISPLAY_ARROWS);
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
        return $this->getData(self::DISPLAY_BULLETS);
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
        return $this->getData(self::IS_RANDOM_ORDER_IMAGE);
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
        return $this->getData(self::SLIDE_IDS);
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
        return $this->getData(self::SLIDE_POSITION);
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
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(BannerExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * Return product model with load conditions
     *
     * @return \Aheadworks\Rbslider\Model\Rule\Product
     */
    public function getProductRule()
    {
        if (!$this->productRule) {
            $conditionArray = $this->conditionConverter->dataModelToArray($this->getProductCondition());
            $this->productRule = $this->productRuleFactory->create();
            $this->productRule->setConditions([])
                ->getConditions()
                ->loadArray($conditionArray);
        }

        return $this->productRule;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        if (is_array($this->getProductCondition())) {
            $this->setProductCondition(serialize($this->getProductCondition()));
        }

        return $this;
    }
}
