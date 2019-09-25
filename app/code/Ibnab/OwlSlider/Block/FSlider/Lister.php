<?php

/**
 * Copyright © 2016 Jake Sharp (http://www.jakesharp.co/) All rights reserved.
 */

namespace Ibnab\OwlSlider\Block\FSlider;

use Ibnab\OwlSlider\Model\Sliders;

class Lister extends \Magento\Catalog\Block\Product\AbstractProduct {

    /**
     * Max number of products in slider
     */
    const PRODUCTS_COUNT = 30;

    /**
     * Products collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productsCollectionFactory;

    /**
     * @var \Ibnab\OwlSlider\Model\Sliders
     */
    protected $sliderFactory;

    /**
     * banner factory
     * @var \Ibnab\MageUnslider\Model\BannersFactory
     */
    protected $_bannerFactory;

    /**
     * bannerslider factory
     * @var \Ibnab\OwlSlider\Model\BannerSliderFactor
     */
    protected $_bannersliderFactory;
    protected $_collectionBestFactory;

    /**
     * Products visibility
     *
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_catalogProductVisibility;

    /**
     * Product reports collection factory
     *
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportsCollectionFactory;

    /**
     * Events type factory
     *
     * @var \Magento\Reports\Model\Event\TypeFactory
     */
    protected $_eventTypeFactory;
    protected $sliderId;

    /**
     * Product slider model
     *
     * @var Ibnab\OwlSlider\Model\Sliders
     */
    protected $_slider;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var Indexer\Category\Flat\State
     */
    protected $flatState;

    /**
     * template
     */
    protected $_objectManager;
    protected $_moduleManager;
    protected $_template = 'Ibnab_OwlSlider::fslider/lister.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context, \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportsCollectionFactory, \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory, \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $_collectionBestFactory, \Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory, \Ibnab\OwlSlider\Model\BannersFactory $bannerFactory, \Ibnab\OwlSlider\Model\BannerSliderFactory $bannersliderFactory, \Magento\Reports\Model\Event\TypeFactory $eventTypeFactory, \Magento\Framework\Url\Helper\Data $urlHelper, \Magento\Framework\Module\Manager $moduleManager,\Magento\Catalog\Model\Indexer\Product\Flat\State $flatState, array $data = []
    ) {
        $this->_productCollectionFactory = $productsCollectionFactory->create();
        $this->_collectionBestFactory = $_collectionBestFactory;
        $this->_reportsCollectionFactory = $reportsCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->sliderFactory = $sliderFactory;
        $this->_bannersliderFactory = $bannersliderFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->urlHelper = $urlHelper;
        $this->_moduleManager = $moduleManager;
        $this->flatState = $flatState;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$this->sliderId  =   $this->getSlider()->getId();
        parent::__construct($context, $data);
    }

    public function setSlider($slider) {
        $this->_slider = $slider;
        return $this;
    }

    /**
     * Get slider id
     *
     * @return int
     */
    public function getSliderId() {
        return $this->_slider->getId();
    }

    /**
     * Get slider
     *
     * @return \JakeSharp\Productslider\Model\Productslider
     */
    public function getSlider() {
        return $this->_slider;
    }

    /**
     * @param int
     *
     * @return this
     */
    public function setSliderId($sliderId) {
        $this->_sliderId = $sliderId;
        $slider = $this->sliderFactory->create()->load($sliderId);
        if ($slider->getId()) {
            $this->setSlider($slider);
            $this->setTemplate($this->_template);
        }

        return $this;
    }

    protected function _getSelectedSliderBanners() {
        $bannersliderFactory = $this->_bannersliderFactory->create();
        $banners = [];
        $slider_id = $this->getSliderId();
        if (!isset($slider_id)) {
            return [];
        }
        $bannersliderSelectedFactory = $bannersliderFactory->getSelectedSliderBanners($slider_id);
        foreach ($bannersliderSelectedFactory as $banner) {
            $banners[] = $banner->getId();
        }
        return $banners;
    }

    public function getBanners() {
        $banner_ids = $this->_getSelectedSliderBanners();
        $todayDateTime = $this->_localeDate->date()->format('Y-m-d H:i:s');
        $collection = $this->_bannerFactory->create()->getCollection()->addFieldToFilter('banner_id', array('in' => $banner_ids))
                ->addFieldToFilter('is_active', Sliders::STATUS_ENABLED)
                ->addFieldToFilter('startTime', [
                    'or' => [
                        0 => ['date' => true, 'lteq' => $todayDateTime],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ])
                ->addFieldToFilter('endTime', [
            'or' => [
                0 => ['date' => true, 'from' => $todayDateTime],
                1 => ['is' => new \Zend_Db_Expr('null')],
            ]
        ]);

        return $collection;
    }

    public function getSliderProductsCollection($type) {
        $collection = "";
        switch ($type) {
            case 'bestsellers':
                $collection = $this->_getBestSellerData($this->_productCollectionFactory);
                break;
            case 'new':
                $collection = $this->_getNewData($this->_productCollectionFactory);
                break;
            case 'mostviewed':
                $collection = $this->_getMostViewedData($this->_productCollectionFactory);
                break;
            case 'onsale':
                $collection = $this->_getOnSaleData($this->_productCollectionFactory);
                break;
            case 'featured':
                $collection = $this->_getSliderFeaturedData($this->_productCollectionFactory);
                break;
            case 'autorelated':
                $collection = $this->_getAutoRelatedData($this->_productCollectionFactory);
                break;
            case 'whobought':
                $collection = $this->_getWhoBoughtData();
                break;
            case 'hdeal':
                $collection = $this->_getHotDealData($this->_productCollectionFactory);
                break;
        }

        return $collection;
    }

    protected function _getWhoBoughtData() {
        if ($this->_moduleManager->isEnabled('Ibnab_WBought')) {
            $frontWhoBought = $this->_objectManager->create('Ibnab\WBought\Block\FrontWidget');
            return $frontWhoBought->getList($this->getProductsCount());
        } else {
            return null;
        }
    }

    protected function _getBestSellerData($collection) {

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()
                ->join(['bestsellers' => $collection->getTable('sales_bestsellers_aggregated_yearly')], 'e.entity_id = bestsellers.product_id AND bestsellers.store_id = ' . $this->getStoreId(), ['qty_ordered', 'rating_pos'])
                ->order('rating_pos');
        $collection->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        return $collection;
    }

    protected function _getMostViewedData($collection) {
        $eventTypes = $this->_eventTypeFactory->create()->getCollection();
        $reportCollection = $this->_reportsCollectionFactory->create();

        // Getting event type id for catalog_product_view event
        foreach ($eventTypes as $eventType) {
            if ($eventType->getEventName() == 'catalog_product_view') {
                $productViewEvent = (int) $eventType->getId();
                break;
            }
        }

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);

        if (!$this->flatState->isFlatEnabled()) {
            $collection->getSelect()->reset()->from(
                    ['report_table_views' => $reportCollection->getTable('report_event')], ['views' => 'COUNT(report_table_views.event_id)']
            )->join(
                    ['e' => $reportCollection->getProductEntityTableName()], $reportCollection->getConnection()->quoteInto(
                            'e.entity_id = report_table_views.object_id', $reportCollection->getProductAttributeSetId()
                    )
            )->where(
                    'report_table_views.event_type_id = ?', $productViewEvent
            )->group(
                    'e.entity_id'
            )->order(
                    'views DESC'
            )->having(
                    'COUNT(report_table_views.event_id) > ?', 0
            );
            $collection->addStoreFilter($this->getStoreId())
                    ->setPageSize($this->getProductsCount())
                    ->setCurPage(1);
//            ->addViewsCount()
        } else {
            $collection->getSelect()->reset()->from(
                    ['report_table_views' => $reportCollection->getTable('report_event')], ['views' => 'COUNT(report_table_views.event_id)']
            )->join(
                    ['e' => $reportCollection->getProductEntityTableName()], $reportCollection->getConnection()->quoteInto(
                            'e.entity_id = report_table_views.object_id', $reportCollection->getProductAttributeSetId()
                    )
            )->join(
                    ['ef' => $reportCollection->getTable('catalog_product_flat_') . $this->getStoreId()], $reportCollection->getConnection()->quoteInto(
                            'ef.entity_id = report_table_views.object_id', $reportCollection->getProductAttributeSetId()
                    )
            )->where(
                    'report_table_views.event_type_id = ?', $productViewEvent
            )->group(
                    'e.entity_id'
            )->order(
                    'views DESC'
            )->limit(
                    $this->getProductsCount()
            )->having(
                    'COUNT(report_table_views.event_id) > ?', 0
            );
            $collection->addAttributeToSelect('ef.name')->addStoreFilter($this->getStoreId())
                    ->setPageSize($this->getProductsCount())
                    ->setCurPage(1);
        }


        return $collection;
    }

    /**
     * Get product slider items based on type
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getNewData($collection) {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
                ->addAttributeToFilter(
                        'news_from_date', ['date' => true, 'to' => $this->getEndOfDayDate()], 'left')
                ->addAttributeToFilter(
                        'news_to_date', [
                    'or' => [
                        0 => ['date' => true, 'from' => $this->getStartOfDayDate()],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                        ], 'left')
                ->addAttributeToSort(
                        'news_from_date', 'desc')
                ->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        return $collection;
    }

    /**
     * Get product slider items based on type
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getHotDealData($collection) {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
                ->addAttributeToFilter(
                        'hot_deal_from', ['date' => true, 'to' => $this->getEndOfDayDate()], 'left')
                ->addAttributeToFilter(
                        'hot_deal_to', [
                    'or' => [
                        0 => ['date' => true, 'from' => $this->getStartOfDayDate()],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                        ], 'left')
                ->addAttributeToSort(
                        'hot_deal_to', 'desc')
                ->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        return $collection;
    }

    /**
     * Get additional-featured slider products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getSliderFeaturedData($collection) {
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()
                ->join(['slider_products' => $collection->getTable('ibnab_owlslider_sliders_products')], 'e.entity_id = slider_products.product_id AND slider_products.slider_id = ' . $this->getSliderId(), ['position'])
                ->order('slider_products.position');
        $collection->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        $this->_productsNumber = $this->getProductsCount() - $collection->count();

        return $collection;
    }

    /**
     * Get on sale slider products
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getOnSaleData($collection) {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
                ->addAttributeToFilter(
                        'special_from_date', ['date' => true, 'to' => $this->getEndOfDayDate()], 'left')
                ->addAttributeToFilter(
                        'special_to_date', [
                    'or' => [
                        0 => ['date' => true, 'from' => $this->getStartOfDayDate()],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                        ], 'left')
                ->addAttributeToSort(
                        'news_from_date', 'desc')
                ->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        return $collection;
    }

    public function getBaseMediaUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getAutoRelatedData($collection) {
        $product = $this->getProduct();

        if (!$product) {
            return;
        }

        $categories = $this->getProduct()->getCategoryIds();

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->addCategoriesFilter(['in' => $categories]);

        $collection->addStoreFilter($this->getStoreId())
                ->setPageSize($this->getProductsCount())
                ->setCurPage(1);

        $collection->addAttributeToFilter('entity_id', array('neq' => $product->getId()));
        $collection->getSelect()->order('rand()');

        return $collection;
    }

    /**
     * @return int
     */
    public function getProductsCount() {
        $items = self::PRODUCTS_COUNT;
        $slider = $this->getSlider();
        if ($slider->getProductsNumber() > 0 && $slider->getProductsNumber() <= self::MAX_PRODUCTS_COUNT) {
            $items = $slider->getProductsNumber();
        }

        return $items;
    }

    /**
     * Get start of day date
     *
     * @return string
     */
    public function getStartOfDayDate() {
        return $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    }

    /**
     * Get end of day date
     *
     * @return string
     */
    public function getEndOfDayDate() {
        return $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product) {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    public function sliderUrl() {
        $id = $this->_request->getParam('slider_id');
        if ($id) {
            return true;
        }
        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice1(\Magento\Catalog\Model\Product $product) {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                    \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE, $product, [
                'include_container' => true,
                'display_minimal_price' => true,
                'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                    ]
            );
        }

        return $price;
    }

    /**
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender() {
        return $this->_layout->createBlock(
                        'Magento\Framework\Pricing\Render', '', ['data' => ['price_render_handle' => 'catalog_product_prices']]
        );
    }

    public function getFormKey() {
        return $this->_layout->createBlock(
                        'Magento\Framework\View\Element\FormKey'
                )->toHtml();
    }

    public function getImage($product, $imageId, $attributes = []) {
        return $this->imageBuilder->setProduct($product)
                        ->setImageId($imageId)
                        ->setAttributes($attributes)
                        ->create();
    }

}
