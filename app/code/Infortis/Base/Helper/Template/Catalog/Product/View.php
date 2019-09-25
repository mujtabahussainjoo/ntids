<?php
/**
 * Product view helper
 */

namespace Infortis\Base\Helper\Template\Catalog\Product;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\BlockFactory;

class View extends AbstractHelper
{
    /**
     * Main helper of the theme
     *
     * @var HelperData
     */
    protected $theme;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * Grid classes
     *
     * @var array
     */
    protected $grid;

    /**
     * Positions of blocks
     *
     * @var array
     */
    protected $position;

    /**
     * Initialization
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        StoreManagerInterface $storeManager,
        BlockFactory $blockFactory
    ) {
        $this->theme = $helper;
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;

        parent::__construct($context);

        $this->calculatePositions();
        $this->calculateGridClasses();
    }

    /**
     * Retrieve positions of header blocks
     */
    protected function calculatePositions()
    {
        $this->position['collateral']   = $this->theme->getCfg('product_page/collateral_position');
        $this->position['related']      = $this->theme->getCfg('product_page/related_position');
        $this->position['upsell']       = $this->theme->getCfg('product_page/upsell_position');
        $this->position['brand']        = $this->theme->getCfg('product_page/brand_position');
    }

    /**
     * Get positions
     *
     * @return array
     */
    public function getPositions()
    {
        return $this->position;
    }

    /**
     * Calculate grid classes for product page sections
     */
    protected function calculateGridClasses()
    {
        //Width (in grid units) of product page sections
        $imgColUnits            = $this->theme->getCfg('product_page/image_column');
        $primColUnits           = $this->theme->getCfg('product_page/primary_column');
        $secColUnits            = $this->theme->getCfg('product_page/secondary_column');
        //$cont2ColUnits          = $this->theme->getCfg('product_page/container2_column'); //$imgColUnits + $primColUnits;
        $lowerPrimColUnits      = $this->theme->getCfg('product_page/lower_primary_column');
        $lowerSecColUnits       = $this->theme->getCfg('product_page/lower_secondary_column');

        //Grid classes
        $prefix = 'grid12-';

        $this->grid['imgCol']         = $prefix . $imgColUnits;

        $this->grid['primCol']        = $prefix . $primColUnits;

        if (!empty($secColUnits))
        {
            $this->grid['secCol']     = $prefix . $secColUnits;
        }

        //$this->grid['cont2Col']       = $prefix . $cont2ColUnits;

        $this->grid['lowerPrimCol']   = $prefix . $lowerPrimColUnits;

        if (!empty($lowerSecColUnits))
        {
            $this->grid['lowerSecCol']    = $prefix . $lowerSecColUnits;
        }
    }

    /**
     * Get grid classes
     *
     * @return array
     */
    public function getGridClasses()
    {
        return $this->grid;
    }

   /**
     * Get static block title
     *
     * @return string
     */
    public function getStaticBlockTitle($id)
    {
        $theBlock = $this->blockFactory->create()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->load($id, 'identifier');

        return $theBlock->getTitle();
    }

    /**
     * Returns path of the related products template
     *
     * @return string
     */
    public function getRelatedProductsTemplate()
    {
        return $this->theme->getCfg('product_page/related_template');
    }

    /**
     * Returns path of the up-sell products template
     *
     * @return string
     */
    public function getUpsellProductsTemplate()
    {
        return $this->theme->getCfg('product_page/upsell_template');
    }

   /**
     * @deprecated
     * Get static block title
     *
     * @return string
     */
    public function getCmsBlockTitle($id)
    {
        return $this->getStaticBlockTitle($id);

        // return ObjectManager::getInstance()
        //     ->create('Magento\Cms\Model\Block')
        //     ->setStoreId($this->storeManager->getStore()->getId())
        //     ->load($id)
        //     ->getTitle();
    }

    /**
     * @deprecated Can be safely removed
     * Check if product collateral data displayed as tabs
     *
     * @return bool
     */
    public function showTabs()
    {
        return $this->theme->getCfg('product_page/tabs');
    }

}
