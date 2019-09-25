<?php

namespace Infortis\Base\Block\Product\ProductList;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Url\Helper\Data as HelperData;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;

class Featured extends ListProduct
{
    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var DesignInterface
     */
    protected $_viewDesignInterface;

    /**
     * @var Session
     */
    protected $_modelSession;

    /**
     * @var Collection
     */
    protected $_productCollection;

    protected $_catalogHelperOutput;
    protected $_categoryFactory;
    protected $_categoryLayerFactory;
    protected $_baseDataHelper;
    protected $_baseLabelHelper;
    protected $_infortisImageHelper;    

    public function __construct(Context $context, 
        PostHelper $postDataHelper, 
        Resolver $layerResolver, 
        CategoryRepositoryInterface $categoryRepository, 
        HelperData $urlHelper, 
        Session $modelSession, 
        Collection $productCollection,
        \Magento\Catalog\Helper\Output $catalogHelperOutput, 
        \Magento\Catalog\Model\Layer\CategoryFactory $categoryLayerFactory,        
        \Infortis\Base\Helper\Data $baseDataHelper,
        \Infortis\Base\Helper\Labels $baseLabelHelper,
        \Infortis\Infortis\Helper\Image $infortisImageHelper, 
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,      
        array $data = []
    ) {
        $this->_modelStoreManagerInterface  = $context->getStoreManager();
        $this->_viewDesignInterface         = $context->getDesignPackage();
        $this->_modelSession                = $modelSession;
        $this->_catalogHelperOutput         = $catalogHelperOutput;
        $this->_categoryFactory             = $categoryFactory;
        $this->_categoryLayerFactory        = $categoryLayerFactory;
        $this->_baseDataHelper            = $baseDataHelper;
        $this->_baseLabelHelper           = $baseLabelHelper;
        $this->_infortisImageHelper           = $infortisImageHelper; 
                        
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected $_collectionCount = NULL;
    protected $_productCollectionId = NULL;
    protected $_cacheKeyArray = NULL;
    
    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addData([
            'cache_lifetime'    => 99999999,
            'cache_tags'        => [Product::CACHE_TAG],
        ]);
    }
    
    public function getDataHelper()
    {
        return $this->_baseDataHelper;
    }
    
    public function getLabelHelper()
    {
        return $this->_baseLabelHelper;
    }
    
    public function getImageHelper()
    {
        return $this->_infortisImageHelper;
    }
    
    public function getCatalogHelperOutput()
    {
        return $this->_catalogHelperOutput;
    }
    
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        if (NULL === $this->_cacheKeyArray)
        {
            $this->_cacheKeyArray = [
                'INFORTIS_ITEMSLIDER',
                $this->_modelStoreManagerInterface->getStore()->getCurrentCurrency()->getCode(),
                //$this->_modelStoreManagerInterface->getStore()->getCurrentCurrencyCode(),
                
                $this->_modelStoreManagerInterface->getStore()->getId(),
//              $this->_viewDesignInterface->getPackageName(), ///
//              $this->_viewDesignInterface->getTheme('template'), 
                //getCode replaces getPackageName and getTheme
                $this->_viewDesignInterface->getDesignTheme()->getCode(),            
                $this->_modelSession->getCustomerGroupId(),
                'template' => $this->getTemplate(),
                
                $this->getBlockName(),
                $this->getCategoryId(),
                $this->getShowItems(),
                $this->getIsResponsive(),
                $this->getBreakpoints(),
                $this->getHideButton(),
                $this->getTimeout(),
                $this->getSortBy(),
                $this->getSortDirection(),
                
                (int)$this->_modelStoreManagerInterface->getStore()->isCurrentlySecure(),
                $this->getUniqueCollectionId(),
            ];
        }
        return $this->_cacheKeyArray;
    }
    
    /**
     * Get collection id
     *
     * @return string
     */
    public function getUniqueCollectionId()
    {
        if (NULL === $this->_productCollectionId)
        {
            $this->_prepareCollectionAndCache();
        }
        return $this->_productCollectionId;
    }
    
    /**
     * Get number of products in the collection
     *
     * @return int
     */
    public function getCollectionCount()
    {
        if (NULL === $this->_collectionCount)
        {
            $this->_prepareCollectionAndCache();
        }
        return $this->_collectionCount;
    }
    
    /**
     * Prepare collection id, count collection
     */
    protected function _prepareCollectionAndCache()
    {
        $ids = [];
        $i = 0;
        foreach ($this->_getProductCollection() as $product)
        {
            $ids[] = $product->getId();
            $i++;
        }
        
        $this->_productCollectionId = implode("+", $ids);
        $this->_collectionCount = $i;
    }
    
    /**
     * Retrieve loaded category collection.
     * Variables collected from CMS markup: category_id, product_count, is_random
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection))
        {
            $categoryID = $this->getCategoryId();
            if($categoryID)
            {               
                $category = $this->_categoryFactory->create();
                $category->load($categoryID);
                $collection = $category->getProductCollection();

                //Sort order parameters
                $sortBy = $this->getSortBy(); //param: sort_by
                if ($sortBy === NULL) //Param not set
                {
                    $sortBy = 'position';
                }
                $sortDirection = $this->getSortDirection(); //param: sort_direction
                if ($sortDirection === NULL) //Param not set
                {
                    $sortDirection = 'ASC';
                }
                $collection->addAttributeToSort($sortBy, $sortDirection);
            }
            else
            {
                $collection = $this->_productCollection;
            }
            $this->_categoryLayerFactory
                ->create()
                ->prepareProductCollection($collection);
            // ObjectManager::getInstance()
            //     ->create('Magento\Catalog\Model\Layer')
            //     ->prepareProductCollection($collection);
            
            if ($this->getIsRandom())
            {
                $collection->getSelect()->order('rand()');
            }
            $collection->addStoreFilter();
            $productCount = $this->getProductCount() ? $this->getProductCount() : 8;
            $collection->setPage(1, $productCount)
                ->load();
            
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
    
    /**
     * Create unique block id for frontend
     *
     * @return string
     */
    public function getFrontendHash()
    {
        return md5(implode("+", $this->getCacheKeyInfo()));
    }
}
