<?php
/**
 * Brand slider
 */

namespace Infortis\Brands\Block;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Infortis\Brands\Helper\Data as HelperData;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Eav\Model\Config as ModelConfig;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\Product\Visibility;

class Brands extends ListBlock
{
    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    protected $_assetRepository;
    
    public function __construct(Context $context, 
        HelperData $helperData, 
        ModelConfig $modelConfig, 
        \Magento\Catalog\Model\Product\Url $productUrl, 
        Collection $productCollection, 
        Visibility $productVisibility, 
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,               
        \Magento\CatalogInventory\Api\StockManagementInterface $modelStock,  
        \Magento\Framework\View\Asset\GroupedCollection $assetCollection,                       
        array $data = []
    )
    {
        $this->_assetRepository = $context->getAssetRepository();
        $this->_modelStoreManagerInterface = $context->getStoreManager();
        $this->_assetCollection = $assetCollection;
        parent::__construct(
            $context,  
            $helperData, 
            $modelConfig, 
            $productUrl, 
            $productCollection, 
            $productVisibility, 
            $productStatus,   
            $modelStock,                                  
            $data,           
            $context->getFilesystem(), 
            $context->getUrlBuilder(), 
            $context->getScopeConfig()
        );
    }

    protected function addAssetToGroup($identifier)
    {
        $this->_assetCollection->add($identifier, 
            $this->_assetRepository->createAsset($identifier));
        return $this;               
    }
    
    protected function _prepareLayout()
    {
        $asset_repository = $this->_assetRepository;
        $asset  = $asset_repository->createAsset('Infortis_Brands::jquery.owlcarousel.min.js');
        $url    = $asset->getUrl();            
        $this->setOwlUrl($url);
        
        $this->addAssetToGroup('legacy/css/itemslider.css');        
    }
    
	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = [
				'BRANDS_SLIDER',
				$this->_modelStoreManagerInterface->getStore()->getId(),
				$this->getTemplateFile(),
				'template' => $this->getTemplate(),
				(int)$this->_modelStoreManagerInterface->getStore()->isCurrentlySecure(),

				$this->getBrandAttributeId(),
				$this->_getFinalCollectionCacheKey(),

				$this->getBlockName(),
				$this->getShowItems(),
				$this->getIsResponsive(),
				$this->getBreakpoints(),
				$this->getTimeout(),
				$this->getLoop(),
			];
		}

		return $this->_cacheKeyArray;
	}
}
