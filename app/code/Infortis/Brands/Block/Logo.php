<?php
/**
 * Product brand
 */

namespace Infortis\Brands\Block;

use Infortis\Brands\Helper\Data as HelperData;
use Magento\Cms\Model\Block;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\CatalogInventory\Model\Stock;
use Magento\Catalog\Block\Product\NewProduct;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

use Magento\Eav\Model\Config as ModelConfig;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\CacheInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Logo extends AbstractBlock
{
    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var Registry
     */
    protected $_frameworkRegistry;

    public function __construct(
        Context $context,         
        HelperData $helperData, 
        ModelConfig $modelConfig, 
        Registry $frameworkRegistry,  
        \Magento\Catalog\Model\Product\Url $productUrl,              
        array $data = []
    )
    {
        $this->_modelStoreManagerInterface = $context->getStoreManager();
        $this->_frameworkRegistry = $frameworkRegistry;

        parent::__construct(
            $context,  
            $helperData, 
            $modelConfig, 
            $productUrl,            
            $data,           
            $context->getFilesystem(), 
            $context->getUrlBuilder(), 
            $context->getScopeConfig()
        );
    }
    
    public function getRegistry()
    {
        return $this->_frameworkRegistry;
    }
	/**
	 * Brand name of the current product
	 *
	 * @var string
	 */
	protected $_currentBrand;

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->addData([
			'cache_lifetime'    => 31536000,
			'cache_tags'        => [Block::CACHE_TAG],
		]);
	}

	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		return [
			'BRANDS_LOGO',
			$this->_modelStoreManagerInterface->getStore()->getId(),
			$this->getTemplateFile(),
			'template' => $this->getTemplate(),
			(int)$this->_modelStoreManagerInterface->getStore()->isCurrentlySecure(),

			$this->getCurrentBrand(),
		];
	}

	/**
	 * Get current product's brand
	 *
	 * @return string
	 */
	public function getCurrentBrand()
	{
		if (NULL === $this->_currentBrand)
		{
			$this->_currentBrand = $this->getBrand($this->_frameworkRegistry->registry('current_product'));
		}
		return $this->_currentBrand;
	}

	/**
	 * Deprecated
	 * Returns current product
	 *
	 * @return \Magento\Catalog\Model\Product
	 */
	public function getCurrentProductObject()
	{
		return $this->_frameworkRegistry->registry('current_product');
	}
}
