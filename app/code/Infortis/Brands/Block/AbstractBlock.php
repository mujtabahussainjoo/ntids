<?php
/**
 * Brand Abstract Block
 */

namespace Infortis\Brands\Block;

use Infortis\Brands\Helper\Data as HelperData;
//use Magento\Catalog\Helper\Product\Url;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;

class AbstractBlock extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Config
     */
    protected $_modelConfig;

    /**
     * @var Filesystem
     */
    protected $_frameworkFilesystem;

    /**
     * @var UrlInterface
     */
    protected $_frameworkUrlInterface;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    /**
     * @var Url
     */
    protected $_productUrl;

    public function __construct(
        Context $context,         
        HelperData $helperData, 
        Config $modelConfig,
        \Magento\Catalog\Model\Product\Url $productUrl,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->_modelConfig = $modelConfig;
        $this->_frameworkFilesystem = $context->getFilesystem();
        $this->_frameworkUrlInterface = $context->getUrlBuilder();
        $this->_configScopeConfigInterface = $context->getScopeConfig();
        $this->_productUrl = $productUrl;
        // $this->_productUrl = $productUrl;

        parent::__construct($context, $data);
    }

	protected $_helper;

	/**
	 * Attribute model
	 *
	 * @var ...
	 */
	protected $_attributeModel = NULL;

    public function getHelperData()
    {
        return $this->_helperData;
    }
    
	/* /////////////////////////////////////////////////////////////////////////////// */


	/**
	 * Path of the brand image folder
	 *
	 * @var string
	 */
	protected $_brandBaseDir;

	/**
	 * Brand URL key separators (page URL and image URL)
	 *
	 * @var array
	 */
	protected $_urlKeySeparator;
	protected $_imageUrlKeySeparator;

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		$this->_helper = $this->_helperData; //Instantiate default helper		
		$this->_brandBaseDir 			= $this->_helper->getBrandImagePath();
		$this->_urlKeySeparator 		= trim($this->_helper->getCfg('general/url_key_separator'));
		$this->_imageUrlKeySeparator 	= trim($this->_helper->getCfg('general/img_url_key_separator'));

		$this->_getAttributeModel();
	}

	/**
	 * Returns attribute model
	 *
	 * @return ...
	 */
	protected function _getAttributeModel()
	{
		if (NULL === $this->_attributeModel)
		{
			$this->_attributeModel = $this->_modelConfig
				->getAttribute('catalog_product', $this->getBrandAttributeId());
		}
		return $this->_attributeModel;
	}

	/**
	 * Returns ID of the brand attribute
	 *
	 * @return string
	 */
	public function getBrandAttributeId()
	{
		return $this->_helper->getCfg('general/attr_id');
	}
	
	/**
	 * Returns name (title) of the brand attribute, set in the admin panel
	 *
	 * @return string
	 */
	public function getBrandAttributeTitle()
	{
		return $this->_attributeModel->getStoreLabel();

		/*$attributeModel = $this->_modelConfig
			->getAttribute('catalog_product', $this->getBrandAttributeId());
		return $attributeModel->getStoreLabel();*/
	}

	/**
	 * Returns brand of the product
	 *
	 * @param Product object
	 * @return string
	 */
	public function getBrand($product)
	{
		$attr = $product->getResource()->getAttribute($this->getBrandAttributeId()); //Attr. object
		return trim($attr->getFrontend()->getValue($product)); //Attr. value
	}

	/**
	 * Returns brand image directory
	 *
	 * @return string
	 */
	protected function _getBrandImageBaseDir()
	{
		return $this->_frameworkFilesystem->getDirectoryWrite('media')->getAbsolutePath() . DIRECTORY_SEPARATOR . $this->_brandBaseDir;
	}

	/**
	 * Returns URL of the brand image directory
	 *
	 * @return string
	 */
	protected function _getBrandImageBaseUrl()
	{
		return $this->_frameworkUrlInterface
			->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $this->_brandBaseDir;
	}
	
	/**
	 * Returns URL of the brand image
	 *
	 * @param string Brand name
	 * @return string
	 */
	public function getBrandImageUrl($brand)
	{
		//Generate image name with simplified brand name
		$manufImageExt = trim($this->_helper->getCfg('general/image_extension'));
		$manufFileName = $this->getBrandUrlKey($brand, $this->_imageUrlKeySeparator) . '.' . $manufImageExt;

		//If image exists, return its URL
		if (file_exists($this->_getBrandImageBaseDir() . $manufFileName))
		{
			return $this->_getBrandImageBaseUrl() . $manufFileName;
		}
		else
		{
			return '';
		}
	}

	/**
	 * Returns URL of the brand page
	 *
	 * @param string Brand name
	 * @return string
	 */
	public function getBrandPageUrl($brand)
	{
		$manufPageUrl = '';
		
		//Check, if brand logo is a link to Magento's search results
		$manufLinkToSearch = $this->_helper->getCfgLinkToSearch();
		
		if ($manufLinkToSearch == 3) //No link at all
		{
			$manufPageUrl = '';
		}
		elseif ($manufLinkToSearch == '2') //Link to advanced search results
		{
			$attributeCode = $this->getBrandAttributeId();
			$attributeOptionId = $this->_attributeModel->getSource()->getOptionId($brand); //TODO check
			$manufPageUrl = $this->_frameworkUrlInterface->getBaseUrl() . 'catalogsearch/advanced/result/?' . $attributeCode . urlencode('[]') . '=' . $attributeOptionId;
		}
		elseif ($manufLinkToSearch == 1) //Link to quick search results
		{
			$manufPageUrl = $this->_frameworkUrlInterface->getBaseUrl() . 'catalogsearch/result/?q=' . str_replace(" ", "+", $brand);
		}
		elseif ($manufLinkToSearch == '0') //Link to custom pages (no link to search results)
		{
			//Get simplified brand name
			$manufUrlKey = $this->getBrandUrlKey($brand, $this->_urlKeySeparator);

			//Get base path of brand pages
			$manufPagePath = trim($this->_helper->getCfg('general/page_base_path'), ' /');
			//$manufPagePath = trim($manufPagePath, '/'); //Strip slashes from the beginning and end
			if ($manufPagePath !== '') //If base path not an empty string, append slash at the end
			{
				$manufPagePath .= '/';
			}

			$manufPageUrl = $this->_frameworkUrlInterface->getBaseUrl() . $manufPagePath . $manufUrlKey;

			//Append category URL suffix if needed and if it exists
			if ($this->_helper->getCfg('general/append_category_suffix'))
			{
				$manufPageUrl .= $this->_configScopeConfigInterface->getValue('catalog/seo/category_url_suffix');
			}
		}
		
		return $manufPageUrl;
	}

	/**
	 * Get brand URL key
	 *
	 * @param string Brand name
	 * @param string URL separator
	 * @return string
	 */
	public function getBrandUrlKey($brand, $separator)
	{
		return $this->_formatBrandUrlKey($brand, $separator);
	}

	/**
	 * Format brand URL key
	 *
	 * @param string Brand name
	 * @param string URL separator
	 * @return string
	 */
	protected function _formatBrandUrlKey($brand, $separator)
	{
		$formattedBrand = $this->_productUrl->formatUrlKey($brand);
		$urlKey = preg_replace('#[^0-9a-z]+#i', $separator, $formattedBrand);
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, $separator);

		return $urlKey;
	}
}
