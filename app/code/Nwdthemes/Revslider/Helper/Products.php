<?php

namespace Nwdthemes\Revslider\Helper;

use \Nwdthemes\Revslider\Helper\Data;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Framework\App\ActionInterface;
use \Magento\Reports\Model\Product\Index;

class Products extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_coreRegistry;
    protected $_categoryFactory;
    protected $_productFactory;
    protected $_indexFactory;
    protected $_stockHelper;
    protected $_storeManager;
    protected $_cartHelper;
    protected $_dataHelper;
    protected $_urlBuilder;
    protected $_urlEncoder;

	private $_products = array();
	private $_categories = array();

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\CatalogInventory\Helper\Stock $stockHelper,
		\Magento\Checkout\Helper\Cart $cartHelper,
		\Magento\Checkout\Helper\Data $dataHelper,
        \Magento\Reports\Model\Product\Index\Factory $indexFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
	) {
        $this->_coreRegistry = $coreRegistry;
        $this->_categoryFactory = $categoryFactory;
        $this->_productFactory = $productFactory;
        $this->_indexFactory = $indexFactory;
        $this->_stockHelper = $stockHelper;
        $this->_storeManager = $storeManager;
        $this->_cartHelper = $cartHelper;
        $this->_dataHelper = $dataHelper;

        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_urlEncoder = $context->getUrlEncoder();

        parent::__construct($context);
	}

	/**
	 * Gets product
	 *
	 * @param	id		$id
	 * @return	array
	 */

	public function getProduct($id) {

		if (isset($this->_products[$id]))
		{
			return $this->_products[$id];
		}

		$product = $this->_productFactory->create()->load($id);
		return $this->_prepareProduct($product);
	}

	/**
	 * Gets products by querey
	 *
	 * @param	array	$query
	 * @return	array
	 */

	public function getProductsByQuery($query) {

		$productsCollection = $this->_productFactory->create()->getCollection();

		if (isset($query['tax_query'][0]['taxonomy']) && $query['tax_query'][0]['taxonomy'] == 'category' && isset($query['tax_query'][0]['terms']) && ! empty($query['tax_query'][0]['terms']) && is_array($query['tax_query'][0]['terms'])) {
			$productsCollection->addCategoriesFilter(['in' => $query['tax_query'][0]['terms']]);
		} elseif (isset($query['post__in']) && ! empty($query['post__in']) && is_array($query['post__in'])) {
			$productsCollection->addFieldToFilter('entity_id', ['in' => $query['post__in']]);
		} else {
			return array();
		}

		$this->_stockHelper->addIsInStockFilterToCollection($productsCollection);

		$productsCollection
			->addStoreFilter($this->_storeManager->getStore())
			->addFieldToFilter('status', Status::STATUS_ENABLED)
			->addFieldToFilter('visibility', array(Visibility::VISIBILITY_BOTH, Visibility::VISIBILITY_IN_CATALOG, Visibility::VISIBILITY_IN_SEARCH))
			->addAttributeToSelect('*');
			
		if (isset($query['orderby'])) {
			$productsCollection->setOrder($query['orderby'], isset($query['order']) ? $query['order'] : 'desc');
		}	

		if (isset($query['showposts'])) {
			$productsCollection->setPageSize($query['showposts']);
		}	
			
		$products = array();
		foreach ($productsCollection as $product) {
			$products[] = $this->_prepareProduct($product);
		}
		
		return $products;
	}

    /**
     * Get current product Id
     * get most recent product viewed if there is no current one
     *
     * @return int
     */

    public function getCurrentProductId() {
        $currentProduct = $this->_coreRegistry->registry('product');
        if ($currentProduct) {
            $currentProductId = $currentProduct->getId();
        } else {
            $recentProducts = $this->_indexFactory->get('viewed')->getCollection()->getAllIds();
            $recentProducts = $this->getProductsByQuery(array('post__in' => $recentProducts, 'showposts' => 1));
            if ($recentProducts) {
                $currentProductId = $recentProducts[0]['ID'];
            } else {
                $currentProductId = false;
            }
        }
        return $currentProductId;
    }

	/**
	 * Gets category
	 *
	 * @param	id		$id
	 * @return	array
	 */

	public function getCategory($id) {

		if (isset($this->_categories[$id]))
		{
			return $this->_categories[$id];
		}

		$category = $this->_categoryFactory->create()->load($id);
		return $this->_prepareCategory($category);
	}

	/**
	 * Gets categories
	 *
	 * @return	array
	 */

	public function getCategories() {

		$categoriesCollection = $this->_categoryFactory->create()
			->getCollection()
			->addAttributeToSelect('name')
			->addAttributeToSort('path', 'asc');

		$categories = array();
		foreach ($categoriesCollection as $category) {
			if ($_category = $this->_prepareCategory($category)) {
				$categories[] = $_category;
			}
		}

		return $categories;
	}

	/**
	 * Prepare product data for slider
	 *
	 * @param object $product
	 * @return array
	 */

	private function _prepareProduct($product) {

		if (isset($this->_products[$product->getId()]))
		{
			return $this->_products[$product->getId()];
		}

		$arrProduct = $product->getData();
		$arrProduct['item'] = $product;

		try{
			$arrProduct['image'] = $product->getImageUrl();
		} catch (\Exception $e) {
            Data::logException($e);
			$arrProduct['image'] = '';
		}

		switch ($product->getTypeId()) {
			case 'configurable' :
				$price = null;
				$specialPrice = null;
				foreach ($product->getTypeInstance()->getUsedProducts($product) as $subProduct) {
					$price = $price ? min($price, $subProduct->getPrice()) : $subProduct->getPrice();
					$specialPrice = $specialPrice ? min($specialPrice, $subProduct->getSpecialPrice()) : $subProduct->getSpecialPrice();
				}
			break;
			case 'simple' :
			default:
				$price = $product->getPrice();
				$specialPrice = $product->getSpecialPrice();
			break;
		}

		$arrProduct['ID'] = $product->getId();
		$arrProduct['post_excerpt'] = $product->getShortDescription();
		$arrProduct['post_content'] = $product->getDescription();
		$arrProduct['post_status'] = 'published';
		$arrProduct['post_category'] = '';
        $arrProduct['cart_link'] = $this->_cartHelper->getAddUrl($product);
        $arrProduct['wishlist_link'] = $this->_urlBuilder->getUrl('wishlist/index/add', ['product' => $product->getId()]);
		$arrProduct['price'] = $price ? $this->_dataHelper->formatPrice($price) : '';
		$arrProduct['special_price'] = $specialPrice ? $this->_dataHelper->formatPrice($specialPrice) : '';
		$arrProduct['view_link'] = $product->getProductUrl();
        $arrProduct['add_to_cart_action_callback'] = $this->_getAddToCartActionCallback($product);

		$this->_products[$product->getId()] = $arrProduct;

		return $arrProduct;
	}

	/**
	 * Prepare category data for slider
	 *
	 * @param object $product
	 * @return array
	 */

	private function _prepareCategory($category) {

		if (isset($this->_categories[$category->getId()]))
		{
			return $this->_categories[$category->getId()];
		}

		$arrCategory = $category->getData();

		if ( ! ($category->getId() > 1 && isset($arrCategory['name']) && isset($arrCategory['level'])))
		{
			return false;
		}

		$arrCategory['count'] = 1;
		$arrCategory['name'] = str_repeat('- ', max(0, $arrCategory['level'] - 1)) . $arrCategory['name'];
		$arrCategory['cat_ID'] = $category->getId();
		$arrCategory['term_id'] = $category->getId();
		$arrCategory['url'] = $category->getUrl($category);

		$this->_categories[$category->getId()] = $arrCategory;

		return $arrCategory;
	}

	/**
     * Generate add to cart form action for callback
     *
     * @param   object  $product
     * @return  string
     */

	public function _getAddToCartActionCallback($product) {
	    $url = $this->_cartHelper->getAddUrl($product);
	    $data = json_encode([
	        'action' => $url,
            'data'   => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->_urlEncoder->encode($url),
            ]
        ]);
	    return "$.mage.dataPost().postData($data);";
    }
	
}