<?php

namespace Infortis\Base\Helper;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Helper\Data as TaxHelperData;

class Rsnippets extends AbstractHelper
{
    const SCHEMA_PRODUCT            = 'itemscope itemtype="http://schema.org/Product"';
    const SCHEMA_OFFER              = 'itemprop="offers" itemscope itemtype="http://schema.org/Offer"';
    const SCHEMA_OFFER_AGGREGATE    = 'itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer"';

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var ScopeConfigInterface
     */
    protected $configScopeConfigInterface;

    /**
     * @var StoreManagerInterface
     */
    protected $modelStoreManagerInterface;

    /**
     * @var TaxHelperData
     */
    protected $taxHelperData;

    public function __construct(
        Context $context,
        HelperData $helperData,
        StoreManagerInterface $modelStoreManagerInterface, 
        TaxHelperData $taxHelperData
    ) {
        $this->helper = $helperData;
        $this->configScopeConfigInterface = $context->getScopeConfigInterface();
        $this->modelStoreManagerInterface = $modelStoreManagerInterface;
        $this->taxHelperData = $taxHelperData;

        parent::__construct($context);
    }

    /**
     * Flag indicating that "AggregateOffer" property must be used instead of "Offer" property
     *
     * @var bool
     */
    protected $_productPageAggregateOffer = false;

    /**
     * Check if rich snippets enabled on product page
     *
     * @return bool
     */
    public function isEnabledOnProductPage()
    {
        return $this->helper->getCfg('rsnippets/enable_product');
    }

    /**
     * Get price rich snippets on product page
     *
     * @param Product
     * @return string
     */
    public function getPriceProperties($product)
    {
        //Get product type ID
        $productTypeId = $product->getTypeId();
        if ($productTypeId === 'grouped')
        {
            return '';
        }

        $includeTax = $this->helper->getCfg('rsnippets/price_incl_tax');
        $html = '<meta itemprop="priceCurrency" content="' . $this->modelStoreManagerInterface->getStore()->getCurrentCurrencyCode() . '" />';

        if ($productTypeId === 'bundle')
        {
            if ($product->getPriceType() == Price::PRICE_TYPE_FIXED)
            {
                $minimalPrice = $this->taxHelperData->getPrice($product, $product->getFinalPrice(), $includeTax);
                $html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
            }
            else
            {
                $pm = $product->getPriceModel(); //ObjectManager::getInstance()->create('Magento\Bundle\Model\Product\Price');

                //TODO: in Magento 1 getPricesDependingOnTax deprecated after 1.5.1.0, see Price::getTotalPrices()
                //Args: product, min/max, include tax
                list($minimalPrice, $maximalPrice) = $pm->getPricesDependingOnTax($product, null, $includeTax);

                //If attribute 'price_view' true, price block is displayed with "As Low as" label.
                if ($product->getPriceView())
                {
                    $html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
                }
                else //Else, display price range. Price snippets must be displayed inside "AggregateOffer" property.
                {
                    $this->_productPageAggregateOffer = true;
                    $html .= '<meta itemprop="lowPrice" content="' . $minimalPrice . '" />';
                    $html .= '<meta itemprop="highPrice" content="' . $maximalPrice . '" />';
                }
            }
        }
        else
        {
            $html .= '<meta itemprop="price" content="' . $this->taxHelperData->getPrice($product, $product->getFinalPrice(), $includeTax) . '" />';
        }

        return $html;
    }

    /**
     * Get offer property and itemscope based on '_productPageAggregateOffer'.
     * IMPORTANT: this method must be called after 'getPriceProperties' in which '_productPageAggregateOffer' is evaluated.
     *
     * @return string
     */
    public function getOfferItemscope()
    {
        if ($this->_productPageAggregateOffer)
        {
            return self::SCHEMA_OFFER_AGGREGATE;
        }
        else
        {
            return self::SCHEMA_OFFER;
        }
    }

    /**
     * Get product itemscope
     *
     * @return string
     */
    public function getProductItemscope()
    {
        return self::SCHEMA_PRODUCT;
    }
}
