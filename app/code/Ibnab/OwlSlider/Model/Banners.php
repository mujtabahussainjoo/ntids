<?php
namespace Ibnab\OwlSlider\Model;

class Banners extends \Magento\Framework\Model\AbstractModel
{
    const BANNER_ID  = 'banner_id';

    /**#@+
     * Slider's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const TARGET_SAME = '_self';
    const TARGET_BLANK = '_blank';
    /**#@-*/

     const BASE_MEDIA_PATH = 'ibnab/owlsliders/images';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\ResourceModel\Banners');
    }
    /**
     * Prepare  statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
     /**
     * Prepare  statuses.
     *
     * @return array
     */
    public function getAvailableTargets()
    {
        return [self::TARGET_SAME => __('Self'), self::TARGET_BLANK => __('Blank')];
    }
    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::BANNER_ID);
    }

}
