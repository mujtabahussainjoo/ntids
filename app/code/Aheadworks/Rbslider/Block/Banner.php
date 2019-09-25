<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block;

use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Model\Source\Position;
use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Model\Source\ImageType;
use Aheadworks\Rbslider\Model\Slide\ImageFileUploader;
use Aheadworks\Rbslider\Api\BlockRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\Rbslider\Model\Source\UikitAnimation;
use Aheadworks\Rbslider\Api\Data\BlockInterface;

/**
 * Class Banner
 * @package Aheadworks\Rbslider\Block
 */
class Banner extends \Magento\Framework\View\Element\Template
{
    /**
     * Path to template file in theme
     * @var string
     */
    protected $_template = 'Aheadworks_Rbslider::block.phtml';

    /**
     * @var BlockRepositoryInterface
     */
    protected $blocksRepository;

    /**
     * @var int|null
     */
    private $blockPosition;

    /**
     * @var int|null
     */
    private $blockType;

    /**
     * @var ImageFileUploader
     */
    private $imageFileUploader;

    /**
     * @var UikitAnimation
     */
    private $uikitAnimation;

    /**
     * @param Context $context
     * @param BlockRepositoryInterface $blocksRepository
     * @param ImageFileUploader $imageFileUploader
     * @param UikitAnimation $uikitAnimation
     * @param array $data
     */
    public function __construct(
        Context $context,
        BlockRepositoryInterface $blocksRepository,
        ImageFileUploader $imageFileUploader,
        UikitAnimation $uikitAnimation,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->blocksRepository = $blocksRepository;
        $this->imageFileUploader = $imageFileUploader;
        $this->uikitAnimation = $uikitAnimation;
    }

    /**
     * Is ajax request or not
     *
     * @return bool
     */
    public function isAjax()
    {
        return $this->_request->isAjax();
    }

    /**
     * Retrieve banners for current block position and type
     *
     * @return BlockInterface[]
     */
    public function getBlocks()
    {
        return $this->blocksRepository
            ->getList($this->getBlockType(), $this->getBlockPosition())
            ->getItems();
    }

    /**
     * Retrieve slide image url
     *
     * @param SlideInterface $slide
     * @return string
     */
    public function getSlideImgUrl(SlideInterface $slide)
    {
        return $slide->getImgType() == ImageType::TYPE_FILE
            ? $this->imageFileUploader->getMediaUrl($slide->getImgFile())
            : $slide->getImgUrl();
    }

    /**
     * Get link for redirect on slider click
     *
     * @param int $slideId
     * @param int $bannerId
     * @return string
     */
    public function getLinkUrl($slideId, $bannerId)
    {
        return $this->getUrl(
            'aw_rbslider/countClicks/redirect',
            [
                'slide_id' => $slideId,
                'banner_id' => $bannerId,
            ]
        );
    }

    /**
     * Retrieve animation effect name by key
     *
     * @param int $key
     * @return string
     */
    public function getAnimation($key)
    {
        return $this->uikitAnimation->getAnimationEffectByKey($key);
    }

    /**
     * Retrieve block position
     *
     * @return int|null
     */
    private function getBlockPosition()
    {
        if (!$this->blockPosition) {
            if (false !== strpos($this->getNameInLayout(), 'menu_top')) {
                $this->blockPosition = Position::MENU_TOP;
            }
            if (false !== strpos($this->getNameInLayout(), 'menu_bottom')) {
                $this->blockPosition = Position::MENU_BOTTOM;
            }
            if (false !== strpos($this->getNameInLayout(), 'content_top')) {
                $this->blockPosition = Position::CONTENT_TOP;
            }
            if (false !== strpos($this->getNameInLayout(), 'page_bottom')) {
                $this->blockPosition = Position::PAGE_BOTTOM;
            }
        }
        return $this->blockPosition;
    }

    /**
     * Retrieve block type
     *
     * @return int|null
     */
    private function getBlockType()
    {
        if (!$this->blockType) {
            if (false !== strpos($this->getNameInLayout(), 'banner_product')) {
                $this->blockType = PageType::PRODUCT_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'banner_category')) {
                $this->blockType = PageType::CATEGORY_PAGE;
            }
            if (false !== strpos($this->getNameInLayout(), 'banner_home')) {
                $this->blockType = PageType::HOME_PAGE;
            }
        }
        return $this->blockType;
    }
}
