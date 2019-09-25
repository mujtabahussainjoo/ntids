<?php
/**
 * List of brands
 */

namespace Infortis\Brands\Block;

use Infortis\Brands\Helper\Data as HelperData;
use Magento\Framework\View\Element\Template\Context;
use Magento\CatalogInventory\Model\Stock;
use Magento\Catalog\Block\Product\NewProduct;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Cms\Model\Block;
use Magento\Eav\Model\Config as ModelConfig;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
class ListBlock extends AbstractBlock
{
    /**
     * @var LoggerInterface
     */
    protected $_logLoggerInterface;

    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var Cache
     */
    protected $_modelCache;

    /**
     * @var ModelConfig
     */
    protected $_modelConfig;

    /**
     * @var Collection
     */
    protected $_productCollection;

    /**
     * @var Visibility
     */
    protected $_productVisibility;

    /**
     * @var Status
     */
    protected $_productStatus;

    /**
     * @var Stock
     */
    protected $_modelStock;

    public function __construct(Context $context, 
        HelperData $helperData, 
        ModelConfig $modelConfig, 
        \Magento\Catalog\Model\Product\Url $productUrl, 
        Collection $productCollection, 
        Visibility $productVisibility, 
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus, 
        \Magento\CatalogInventory\Api\StockManagementInterface $modelStock,                      
        array $data = []        
    )
    {
        $this->_logLoggerInterface          = $context->getLogger();
        $this->_modelStoreManagerInterface  = $context->getStoreManager();
        $this->_modelCache                  = $context->getCache();
        $this->_modelConfig                 = $context->getScopeConfig();
        $this->_productCollection           = $productCollection;
        $this->_productVisibility           = $productVisibility;
        $this->_productStatus               = $productStatus;
        $this->_modelStock                  = $modelStock;
        
        parent::__construct(
            $context, 
            $helperData, 
            $modelConfig, 
            $productUrl, 
            $data
        );
    }

	const CACHE_TAG = 'brands_list';

	/**
	 * Block cache key informative items
	 *
	 * @var array
	 */
	protected $_cacheKeyArray = NULL;


	/* /////////////////////////////////////////////////////////////////////////////// */


	/**
	 * Brand collection
	 *
	 * @var array
	 */
	protected $_brandCollection = NULL;

	/**
	 * Cache key of requested brand collection (brands selected by admin)
	 * Uniqe identifier of brand collection in cache.
	 *
	 * @var string
	 */
	protected $_collectionCacheKey = NULL;

	/**
	 * Cache key of final brand collection (brands which will be rendered)
	 *
	 * @var string
	 */
	protected $_finalCollectionCacheKey = NULL;

	/**
	 * Selected brands string (from param or from global config)
	 *
	 * @var string
	 */
	protected $_selectedBrandsString = NULL;

	/**
	 * Flag: use all brands or selected brands
	 *
	 * @var array
	 */
	protected $_flagUseAllBrands = true;

	/**
	 * Brand URL keys
	 *
	 * @var array
	 */
	protected $_urlKeys = NULL;

	/**
	 * Cache tags
	 *
	 * @var array
	 */
	protected $_collectionCacheTags = [Attribute::CACHE_TAG, self::CACHE_TAG];

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		///$this->_logLoggerInterface->debug('_construct'); ///
		parent::_construct();

		$this->addData([
			'cache_lifetime'    => 31536000,
			'cache_tags'        => $this->_collectionCacheTags,
			//'cache_tags'        => array(Attribute::CACHE_TAG),
			//'cache_tags'        => array(Block::CACHE_TAG),
			//'cache_tags'        => array(Product::CACHE_TAG),
		]);

		$this->_flagUseAllBrands = true;

		$this->_prepareTheCollection();
	}

	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		///$this->_logLoggerInterface->debug('getCacheKeyInfo: List'); ///
		if (NULL === $this->_cacheKeyArray)
		{
			///$this->_logLoggerInterface->debug('getCacheKeyInfo -- KEY===NULL'); ///
			$this->_cacheKeyArray = [
				'BRANDS_LIST',
				$this->_modelStoreManagerInterface->getStore()->getId(),
				$this->getTemplateFile(),
				'template' => $this->getTemplate(),
				(int)$this->_modelStoreManagerInterface->getStore()->isCurrentlySecure(),

				$this->getBrandAttributeId(),
				$this->_getFinalCollectionCacheKey(),
				//IMPORTANT: Tutaj powinien byc tylko hash z kolekcji wynikowej aby nie odswiezac cache calego bloku zbyt czesto.
			];
		}

		//
		///$this->_logLoggerInterface->debug('getCacheKeyInfo -- KEY md5= ' . md5(implode("+", $this->_cacheKeyArray))); ///
		//
		return $this->_cacheKeyArray;
	}

	/**
	 * Get collection id
	 *
	 * @return string
	 */
	protected function _getFinalCollectionCacheKey()
	{
		///$this->_logLoggerInterface->debug('_getFinalCollectionCacheKey'); ///
		if (NULL === $this->_finalCollectionCacheKey)
		{
			$this->_prepareTheCollection();
		}
		return $this->_finalCollectionCacheKey;
	}

	/**
	 * Get cache key of brand collection (uniqe identifier of brand collection in cache)
	 *
	 * @return string
	 */
	protected function _getCollectionCacheKey()
	{
		///$this->_logLoggerInterface->debug('_getCollectionCacheKey'); ///
		if (NULL === $this->_collectionCacheKey)
		{
			$this->_prepareTheCollection();
		}
		return $this->_collectionCacheKey;
	}



	/**
	 * ///////////////////////////////////////////////////////////////////////////////
	 * Prepare collection
	 */
	protected function _prepareTheCollection()
	{
	///$this->_logLoggerInterface->debug('>>> _prepareTheCollection'); ///

		$this->_prepareSelectedBrandsAndFlags();
		$this->_prepareCollectionCacheKey();
		$brands = $this->_getBrandCollection();
		$this->_finalCollectionCacheKey = md5(implode("+", $brands));

	///$this->_logLoggerInterface->debug('<<< _prepareTheCollection -- _finalCollectionCacheKey= ' . $this->_finalCollectionCacheKey); ///
	}

	/**
	 * Prepare flag: use all brands or selected brands.
	 * Prepare string with selected brands.
	 * Important: this method has to be called before other methods which prepare the collection
	 */
	protected function _prepareSelectedBrandsAndFlags() //TODO adjust name to functions
	{
		///$this->_logLoggerInterface->debug('_prepareSelectedBrandsAndFlags'); ///

		//TODO move here retrieving of selected

		//If brand list is provided via parameter, it overrides brands selection from config
		$selectedBrands = $this->getBrands(); //parameter: brands
		if ($selectedBrands === NULL) //Param not set
		{
			if ($this->_helper->getCfg('list/all_brands'))
			{
				$this->_flagUseAllBrands = true;
			}
			else
			{
				$this->_flagUseAllBrands = false;
				$this->_selectedBrandsString = $this->_helper->getCfg('list/brands'); //Get string with brand list from config
			}
		}
		else //Param is set
		{
			$this->_flagUseAllBrands = false;
			$this->_selectedBrandsString = $selectedBrands; //Get string with brand list from parameter
		}
	}

	/**
	 * Prepare collection cache key
	 *
	 * @return string
	 */
	protected function _prepareCollectionCacheKey()
	{
		///$this->_logLoggerInterface->debug('_prepareCollectionCacheKey'); ///

		//Other variables
		$key[] = 'brands';
		$key[] = $this->_modelStoreManagerInterface->getStore()->getId();

		//Basic variables
		if ($this->_flagUseAllBrands)
		{
			//If all brands, add empty item
			//$key[] = '';
		}
		else
		{
			//If not all brands, add string with selected brands
			$key[] = $this->_selectedBrandsString;
		}
		$key[] = $this->_helper->getCfg('list/assigned');
		$key[] = $this->_helper->getCfg('list/assigned_in_stock');

		$this->_collectionCacheKey = 'brands-' . md5(implode("|", $key)); //IMPORTANT: has to be hash, too long key will not create cache

		///$this->_logLoggerInterface->debug('_prepareCollectionCacheKey -- key= ' . $this->_collectionCacheKey); ///
	}

	/**
	 * Get the collection of brands
	 *
	 * @return array
	 */
	protected function _getBrandCollection()
	{
		///$this->_logLoggerInterface->debug('%%%cache%%%'); ///
		if (NULL === $this->_brandCollection)
		{
			///$this->_logLoggerInterface->debug('%%%cache%%% -- NULL'); ///
			$cache = $this->_modelCache;
			$key = $this->_getCollectionCacheKey();
			if (! $data = $cache->load($key))
			{
				///$this->_logLoggerInterface->debug('%%%cache%%% -- COLLECTION IS NOT IN CACHE !!! !!!'); ///
				$brands = $this->_buildBrandsCollection();
				$this->_brandCollection = $brands;
				///$this->_logLoggerInterface->debug('%%%cache%%% -- serialize = ' . serialize($brands)); ///

				//Save in cache
				$data = urlencode(serialize($brands));
				$cache->save($data, $key, $this->_collectionCacheTags, 2592000); //30 days: 3600*24*30

				///$this->_logLoggerInterface->debug('%%%cache%%% -- urlencode = ' . $data); ///
			}
			else
			{
				///$this->_logLoggerInterface->debug('%%%cache%%% -- COLLECTION GET FROM CACHE = ' . $data); ///

				//Get from cache
				$this->_brandCollection = unserialize(urldecode($data));
			}

			if (!$this->_brandCollection)
			{
				$this->_brandCollection = [];
			}
		}
		return $this->_brandCollection;
	}



	/**
	 * ///////////////////////////////////////////////////////////////////////////////
	 * Create the collection of brands
	 *
	 * @return array
	 */
	protected function _buildBrandsCollection()
	{
		///$this->_logLoggerInterface->debug('>>> >>> _buildBrandsCollection'); ///
		$showAssignedToProducts = $this->_helper->getCfg('list/assigned');

		if ($this->_flagUseAllBrands)
		{
			if ($showAssignedToProducts)
			{
				$brands = $this->_getAllBrandsInUse();
			}
			else
			{
				$brands = $this->_getAllBrands();
			}
		}
		else //Only selected brands
		{
			if ($showAssignedToProducts)
			{
				$brands = $this->_getAllBrandsInUse();
				///$this->_logLoggerInterface->debug('_buildBrandsCollection -- SELECTED ASSIGNED');

				$selectedBrands = $this->_getSelectedBrands();
				$brands = array_intersect($selectedBrands, $brands);
				///$this->_logLoggerInterface->debug('_buildBrandsCollection -- returned brands       -- count = ' . count($brands)); ///
			}
			else
			{
				$brands = $this->_getSelectedBrands();
				///$this->_logLoggerInterface->debug('_buildBrandsCollection -- SELECTED !assigned');
				///$this->_logLoggerInterface->debug('_buildBrandsCollection -- selectedBrands -- count = ' . count($brands)); ///
			}
		}

		///$this->_logLoggerInterface->debug('<<< <<< _buildBrandsCollection'); ///
		return $brands;
	}

	/**
	 * Get selected brands: from param or from global config
	 *
	 * @return array
	 */
	protected function _getSelectedBrands()
	{
		///$this->_logLoggerInterface->debug('_getSelectedBrands'); ///
		$brandString = $this->_selectedBrandsString; //$this->_getSelectedBrandsString();

		//Get array of brands from string
		if (!empty($brandString))
		{
			return array_map('trim', explode(',', $brandString));
			//return explode(',', $brandString);
		}
		else
		{
			return [];
		}
	}

	/**
	 * Returns all existing brands
	 *
	 * @return array
	 */
	protected function _getAllBrands()
	{
		///$this->_logLoggerInterface->debug('_getAllBrands'); ///

		/*$attributeModel = $this->_modelConfig
			->getAttribute('catalog_product', $this->getBrandAttributeId());*/
			
		/*
		getAllOptions ([bool $withEmpty = true], [bool $defaultValues = false])
			- bool $withEmpty: Add empty option to array
			- bool $defaultValues: Return default values
		*/
		$options = [];
		foreach ($this->_attributeModel->getSource()->getAllOptions(false, true) as $o)
		{
			$options[] = $o['label'];
		}
		
		return $options;
	}
	
	/**
	 * Returns only brands, which are currently assigned to products
	 *
	 * @return array
	 */
	protected function _getAllBrandsInUse()
	{
		///$this->_logLoggerInterface->debug('_getAllBrandsInUse'); ///
		$attributeCode = $this->getBrandAttributeId();
		/*$attributeModel = $this->_modelConfig
			->getAttribute('catalog_product', $attributeCode);*/
		
		//Get product collection
		$products = $this->_productCollection
			->addAttributeToSelect($attributeCode)
			->addAttributeToFilter($attributeCode, ['neq' => ''])
			->addAttributeToFilter($attributeCode, ['notnull' => true])
			->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
			->addStoreFilter($this->_modelStoreManagerInterface->getStore()->getId())
			;

		//TODO: check how to add visibility filter
		//from: NewProduct
		//$collection->setVisibility($this->_productVisibility->getVisibleInCatalogIds());
		//OR:
		//from: Layer
		//$this->_productStatus->addVisibleFilterToCollection($collection);
		//$this->_productVisibility->addVisibleInCatalogFilterToCollection($collection);

		//Filter brands which are currently assigned to products which are in stock
		if ($this->_helper->getCfg('list/assigned_in_stock'))
		{
			$this->_modelStock->addInStockFilterToCollection($products); // TODO: check app/code/Magento/CatalogInventory/Api/StockItemCriteriaInterface
		}

		//Get all (attribute's) values in use
		$attributeValuesInUse = array_unique($products->getColumnValues($attributeCode));

		//Get attribute options (text labels)
		$optionLabels = $this->_attributeModel->getSource()->getOptionText(
			implode(',', $attributeValuesInUse)
			);

		//If only one option retrieved (in that case it is string), convert to array
		if (is_string($optionLabels))
		{
			return [$optionLabels];
		}
		return $optionLabels;
	}



	/**
	 * ///////////////////////////////////////////////////////////////////////////////
	 * Get loaded list of brands
	 * Wrapper for the protected method
	 *
	 * @return array
	 */
	public function getLoadedBrands()
	{
		///$this->_logLoggerInterface->debug('FRONTEND getBrandsList'); ///
		return $this->_getBrandCollection();
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



	/**
	 * ///////////////////////////////////////////////////////////////////////////////
	 * Get brand URL key
	 * Override base method. Get URL from already prepared hashtable.
	 *
	 * @param string Brand name
	 * @param string URL separator
	 * @return string
	 */
	public function getBrandUrlKey($brand, $separator)
	{
		if (FALSE === isset($this->_urlKeys[$separator][$brand]))
		{
			$this->_urlKeys[$separator][$brand] = $this->_formatBrandUrlKey($brand, $separator);
		}
		return $this->_urlKeys[$separator][$brand];
	}
}
