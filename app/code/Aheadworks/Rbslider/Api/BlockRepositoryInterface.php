<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Rbslider block repository interface
 *
 * @api
 */
interface BlockRepositoryInterface
{
    /**
     * Retrieve block(s) matching the specified blockType and blockPosition
     * Update views block statistics if necessary
     *
     * @param int $blockType
     * @param int $blockPosition
     * @param bool $allBlocks
     * @param bool $updateViewsStatistic
     * @param int $bannerId
     *
     * @return \Aheadworks\Rbslider\Api\Data\BlockSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList($blockType, $blockPosition, $allBlocks = true, $updateViewsStatistic = true, $bannerId = 0);
}
