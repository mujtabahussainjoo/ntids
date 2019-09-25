<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Block;

class Membership extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Default toolbar block name.
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';

    /**
     * @var array
     */
    protected $_priceBlock = [];

    /**
     * Product factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @var ReviewRendererInterface
     */
    protected $reviewRenderer;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistHelper;

    /**
     * Review model factory.
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;
    
    /**
     * Membership factory
     *
     * @var \Magedelight\MembershipSubscription\Model\MembershipProductsFactory
     */
    protected $_MembershipProductsFactory;
    
    /**
     *
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magedelight\MembershipSubscription\Model\MembershipProductsFactory $MembershipProductsFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->imageBuilder = $context->getImageBuilder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->_productFactory = $productFactory;
        $this->urlHelper = $urlHelper;
        $this->_cartHelper = $context->getCartHelper();
        $this->reviewRenderer = $context->getReviewRenderer();
        $this->_wishlistHelper = $context->getWishlistHelper();
        $this->_reviewFactory = $reviewFactory;
        $this->_MembershipProductsFactory = $MembershipProductsFactory;
        $this->_priceCurrency = $priceCurrency;
        parent::__construct($context, $data);

       
        $product_collection = $this->_productFactory->create()->getCollection();
        $product_collection->addAttributeToSelect('*');
        $product_collection->getSelect()->join(
            ['magedelight_membership_products' => $product_collection->getTable('magedelight_membership_products')],
            'e.entity_id = magedelight_membership_products.product_id',
            ['product_id','product_name']
        );
        
        $product_collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        
        $this->setCollection($product_collection);
    }

    public function _prepareLayout()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
        $pageTitle = $this->scopeConfig->getValue(
            'membership/membership_settings/page_title',
            $storeScope
        );
      
        
        $metaKeywords = $this->scopeConfig->getValue(
            'membership/membership_settings/meta_keywords',
            $storeScope
        );
        
        $metaDescription = $this->scopeConfig->getValue(
            'membership/membership_settings/meta_description',
            $storeScope
        );
        
        $this->pageConfig->getTitle()->set(__($pageTitle));
        
        $this->pageConfig->setKeywords($metaKeywords);
        
        $this->pageConfig->setDescription($metaDescription);

        parent::_prepareLayout();
        if ($this->getCollection()) {
            // create pager block for collection

            $toolbar = $this->getToolbarBlock();

            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'list.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $toolbar->setChild('product_list_toolbar_pager', $pager); // set pager block in layout
            // called prepare sortable parameters
            $collection = $this->getCollection();

            // use sortable parameters
            $orders = $this->getAvailableOrders();

            if ($orders) {
                $toolbar->setAvailableOrders($orders);
            }
            $sort = $this->getSortBy();
            if ($sort) {
                $toolbar->setDefaultOrder($sort);
            }
            $dir = $this->getDefaultDirection();
            if ($dir) {
                $toolbar->setDefaultDirection($dir);
            }
            $modes = $this->getModes();
            if ($modes) {
                $toolbar->setModes($modes);
            }
            $toolbar->setCollection($collection);

            $this->setChild('toolbar', $toolbar);
            $this->getCollection()->load();
        }

        return $this;

        //return parent::_prepareLayout();
    }

    /**
     *
     * @return type
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     *
     * @return type
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    /**
     * Retrieve product image.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $imageId
     * @param array                          $attributes
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
                        ->setImageId($imageId)
                        ->setAttributes($attributes)
                        ->create();
    }

    /**
     * Whether redirect to cart enabled.
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve current view mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Return HTML block with price.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        
        $productId = $product->getID();
        if ($productId) {
            $model = $this->_MembershipProductsFactory->create();
            $model->load($productId, 'product_id');
            $membershipDuration = $model->getMembershipDuration();
            $featured = $model->getFeatured();
            
            $return = [];
            $durationArray = unserialize($membershipDuration);
            if (count($durationArray)>0) {
                $newDurations = $this->arraySortByColumn($durationArray, 'price', SORT_ASC);
                if (count($newDurations)>1) {
                    $currencySymbol = $this->getCurrentCurrencySymbol();
                    $price = $newDurations[0]['price'];
                    $returnPrice =  "<span class='price-label'>Starting Price</span><span class='price'>".$currencySymbol.$price."</span>";
                } elseif (count($newDurations)==1) {
                    $currencySymbol = $this->getCurrentCurrencySymbol();
                    $price = $newDurations[0]['price'];
                    $returnPrice = "<span class='price'>".$currencySymbol.$price."<span>";
                }
                
                $return['price'] = $returnPrice;
                $return['featured'] = $featured;
                
                return $return;
            }
        }
    }
    
    
    /**
     *
     * @param type $array
     * @param type $column
     * @param type $sort
     * @return type
     */
    public function arraySortByColumn(&$array, $column, $sort)
    {
        $count = [];
        foreach ($array as $key => $row) {
            $count[$key] = $row[$column];
        }

        array_multisort($count, $sort, $array);
        
        return $array;
    }
    
    /**
     * Get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }

    /**
     * Return HTML block with tier price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $priceType
     * @param string                         $renderZone
     * @param array                          $arguments
     *
     * @return string
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        $product->getId();
        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }

        return $price;
    }

    /**
     * Get post parameters.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);

        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl($url),
            ],
        ];
    }

    /**
     * Retrieve url for add product to cart
     * Will return product view page URL if product has required options.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $additional
     *
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        if ($product->getTypeInstance()->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = [];
            }
            $additional['_query']['options'] = 'cart';

            return $this->getProductUrl($product, $additional);
        }

        return $this->_cartHelper->getAddUrl($product, $additional);
    }

    /**
     * Retrieve Product URL using UrlDataObject.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array                          $additional the route params
     *
     * @return string
     */
    public function getProductUrl($product, $additional = [])
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }

            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * Check Product has URL.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get product reviews summary.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool                           $templateType
     * @param bool                           $displayIfNoReviews
     *
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());

        return $this->reviewRenderer->getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * Retrieve product details html.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return mixed
     */
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);

            return $renderer->toHtml();
        }

        return '';
    }

    /**
     * @param null $type
     *
     * @return bool|\Magento\Framework\View\Element\AbstractBlock
     */
    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }

        return;
    }

    /**
     * @return \Magento\Framework\View\Element\RendererList
     */
    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'details.renderers'
        );
    }

    /**
     * Retrieve add to wishlist params.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }
}
