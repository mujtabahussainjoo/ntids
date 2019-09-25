<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Api\Data;

/**
 * Statistic interface
 * @api
 */
interface StatisticInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const SLIDE_BANNER_ID = 'slide_banner_id';
    const VIEW_COUNT = 'view_count';
    const CLICK_COUNT = 'click_count';
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
     * Get slide banner id
     *
     * @return int
     */
    public function getSlideBannerId();

    /**
     * Set slide banner id
     *
     * @param int $slideBannerId
     * @return $this
     */
    public function setSlideBannerId($slideBannerId);

    /**
     * Get view count
     *
     * @return int
     */
    public function getViewCount();

    /**
     * Set view count
     *
     * @param int $viewCount
     * @return $this
     */
    public function setViewCount($viewCount);

    /**
     * Get click count
     *
     * @return int
     */
    public function getClickCount();

    /**
     * Set click count
     *
     * @param int $clickCount
     * @return $this
     */
    public function setClickCount($clickCount);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Aheadworks\Rbslider\Api\Data\StatisticExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Aheadworks\Rbslider\Api\Data\StatisticExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Rbslider\Api\Data\StatisticExtensionInterface $extensionAttributes
    );
}
