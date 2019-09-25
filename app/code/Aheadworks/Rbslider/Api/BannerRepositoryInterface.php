<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Banner CRUD interface
 * @api
 */
interface BannerRepositoryInterface
{
    /**
     * Save banner
     *
     * @param \Aheadworks\Rbslider\Api\Data\BannerInterface $banner
     * @return \Aheadworks\Rbslider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Rbslider\Api\Data\BannerInterface $banner);

    /**
     * Retrieve banner
     *
     * @param int $bannerId
     * @return \Aheadworks\Rbslider\Api\Data\BannerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($bannerId);

    /**
     * Retrieve banners matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Rbslider\Api\Data\BannerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete banner
     *
     * @param \Aheadworks\Rbslider\Api\Data\BannerInterface $banner
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Rbslider\Api\Data\BannerInterface $banner);

    /**
     * Delete banner by ID
     *
     * @param int $bannerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerId);
}
