<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders;

class Preview extends \Magento\Catalog\Block\Product\AbstractProduct {

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
    protected $_request;
    protected $_storeManager;

    protected $paramsOwl = ['items,itemsDesktop', 'itemsDesktopSmall', 'itemsTablet', 'itemsTabletSmall', 'itemsMobile', 'itemsCustom', 'singleItem', 'itemsScaleUp', 'slideSpeed',
        'paginationSpeed', 'rewindSpeed', 'autoPlay', 'stopOnHover', 'navigation', 'navigationText', 'rewindNav', 'scrollPerPage', 'pagination', 'paginationNumbers',
        'responsive', 'responsiveRefreshRate', 'responsiveBaseWidth', 'baseClass', 'theme', 'lazyLoad', 'lazyFollow', 'lazyEffect', 'autoHeight', 'jsonPath', 'jsonSuccess',
        'dragBeforeAnimFinish', 'mouseDrag', 'touchDrag', 'addClassActive', 'transitionStyle'];

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
    \Magento\Catalog\Block\Product\Context $context,\Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportsCollectionFactory,\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollectionFactory, \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $_collectionBestFactory,  \Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory, \Ibnab\OwlSlider\Model\BannersFactory $bannerFactory, 
    \Ibnab\OwlSlider\Model\BannerSliderFactory $bannersliderFactory,\Magento\Reports\Model\Event\TypeFactory $eventTypeFactory, array $data = []
    ) {
        $this->_productCollectionFactory = $productsCollectionFactory->create();
        $this->_collectionBestFactory = $_collectionBestFactory;
        $this->_reportsCollectionFactory = $reportsCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_eventTypeFactory = $eventTypeFactory;
        $this->_request = $context->getRequest();
        $this->_storeManager = $context->getStoreManager();
        $this->sliderFactory = $sliderFactory;
        $this->_bannersliderFactory = $bannersliderFactory;
        $this->_bannerFactory = $bannerFactory;
        $this->sliderId  =   $this->getSlider()->getId();
        parent::__construct($context, $data);
    }

    public function getSlider() {
        $id = $this->_request->getParam('slider_id');
        $model = $this->sliderFactory->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            return $model;
        }
    }

    protected function _getSelectedSliderBanners() {
        $bannersliderFactory = $this->_bannersliderFactory->create();
        $banners = [];
        $slider_id = $this->_request->getParam('slider_id');
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
        $collection = $this->_bannerFactory->create()->getCollection()->addFieldToFilter('banner_id', array('in' => $banner_ids));
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
        }

        return $collection;
    }

    protected function _getBestSellerData($collection) {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()
                    ->join(['bestsellers' => $collection->getTable('sales_bestsellers_aggregated_yearly')],
                                'e.entity_id = bestsellers.product_id AND bestsellers.store_id = '.$this->getStoreId(),
                                ['qty_ordered','rating_pos'])
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
                $productViewEvent = (int)$eventType->getId();
                break;
            }
        }

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()->reset()->from(
                    ['report_table_views' => $reportCollection->getTable('report_event')],
                    ['views' => 'COUNT(report_table_views.event_id)']
                )->join(
                    ['e' => $reportCollection->getProductEntityTableName()],
                    $reportCollection->getConnection()->quoteInto(
                        'e.entity_id = report_table_views.object_id',
                        $reportCollection->getProductAttributeSetId()
                    )
                )->where(
                    'report_table_views.event_type_id = ?',
                    $productViewEvent
                )->group(
                    'e.entity_id'
                )->order(
                    'views DESC'
                )->having(
                    'COUNT(report_table_views.event_id) > ?',
                    0
                );

        $collection->addStoreFilter($this->getStoreId())
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1);
//            ->addViewsCount()

        return $collection;
    }
    /**
     * Get product slider items based on type
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getNewData($collection)
    {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addAttributeToFilter(
                'news_from_date',
                ['date' => true, 'to' => $this->getEndOfDayDate()],
                'left')
            ->addAttributeToFilter(
                'news_to_date',
                [
                    'or' => [
                        0 => ['date' => true, 'from' => $this->getStartOfDayDate()],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ],
                'left')
            ->addAttributeToSort(
               'news_from_date',
               'desc')
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
    protected function _getSliderFeaturedData($collection)
    {
        $collection = $this->_addProductAttributesAndPrices($collection);
        $collection->getSelect()
                    ->join(['slider_products' => $collection->getTable('ibnab_owlslider_sliders_products')],
                            'e.entity_id = slider_products.product_id AND slider_products.slider_id = '.$this->sliderId ,
                            ['position'])
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
    protected function _getOnSaleData($collection)
    {
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addAttributeToFilter(
                'special_from_date',
                ['date' => true, 'to' => $this->getEndOfDayDate()],
                'left')
            ->addAttributeToFilter(
                'special_to_date',
                [
                    'or' => [
                        0 => ['date' => true, 'from' => $this->getStartOfDayDate()],
                        1 => ['is' => new \Zend_Db_Expr('null')],
                    ]
                ],
                'left')
            ->addAttributeToSort(
                'news_from_date',
                'desc')
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
    protected function _getAutoRelatedData($collection)
    {
        $product = $this->getProduct();

        if(!$product){
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
    public function getStartOfDayDate()
    {
        return $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
    }

    /**
     * Get end of day date
     *
     * @return string
     */
    public function getEndOfDayDate()
    {
        return $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
    }

    /**
     * @return int
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

}
