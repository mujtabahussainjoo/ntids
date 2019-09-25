<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Widget;

use Aheadworks\Rbslider\Model\Source\Position;
use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Api\Data\BlockInterface;

/**
 * Class Banner
 * @package Magento\Blog\Block\Widget
 */
class Banner extends \Aheadworks\Rbslider\Block\Banner implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var string
     */
    const WIDGET_NAME_PREFIX = 'aw_rbslider_widget_';

    /**
     * Retrieve banner for widget
     *
     * @return BlockInterface[]
     */
    public function getBlocks()
    {
        $bannerId = $this->getData('banner_id');
        $blocks = $this->blocksRepository
            ->getList(PageType::CUSTOM_WIDGET, Position::CONTENT_TOP, true, true, $bannerId)
            ->getItems();

        foreach ($blocks as $block) {
            if ($block->getBanner()->getId() == $bannerId) {
                return [$block];
            }
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getNameInLayout()
    {
        return self::WIDGET_NAME_PREFIX . $this->getData('banner_id');
    }
}
