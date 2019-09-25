<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab;

use Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Slide;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Slides
 *
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab
 */
class Slides extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'banner/edit/slides.phtml';

    /**
     * @var Slide
     */
    private $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid()
    {
        if (!$this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Slide::class,
                'slide.banner.grid'
            );
        }
        return $this->blockGrid;
    }
}
