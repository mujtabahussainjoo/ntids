<?php

namespace Infortis\Base\Helper;

use Infortis\Base\Helper\Data as HelperData;
use Infortis\Base\Helper\GetNowBasedOnLocale;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Date;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

class Labels extends AbstractHelper
{
    /**
     * Is "new" label enabled
     *
     * @var bool
     */
    protected $labelNewEnabled = true;

    /**
     * Is "saved value" label enabled
     *
     * @var bool
     */
    protected $labelSavedValueEnabled = true;

    /**
     * Number of decimal digits to round to
     *
     * @var int
     */
    protected $savedValuePrecision = 2;

    /**
     * Is "sale" label enabled
     *
     * @var bool
     */
    protected $labelSaleEnabled = true;

    /**
     * Is custom label enabled
     *
     * @var bool
     */
    protected $labelCustomEnabled = false;

    /**
     * Custom label attribute code
     *
     * @var string
     */
    protected $customLabelAttributeCode;

    /**
     * Style of labels - round
     *
     * @var bool
     */
    protected $roundLabels = false;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var GetNowBasedOnLocale
     */
    protected $getNowBasedOnLocale;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;
    
    public function __construct(
        Context $context,
        HelperData $helperData,
        GetNowBasedOnLocale $getNowBasedOnLocale,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->helper = $helperData;
        $this->getNowBasedOnLocale = $getNowBasedOnLocale;
        $this->attributeRepository = $attributeRepository;

        $this->labelNewEnabled = $this->helper->getCfg('product_labels/new');
        $this->labelSaleEnabled = $this->helper->getCfg('product_labels/sale');
        $this->labelSavedValueEnabled = $this->helper->getCfg('product_labels/saved_value');
        $this->savedValuePrecision = $this->helper->getCfg('product_labels/saved_value_precision');
        $this->roundLabels = $this->helper->getCfg('product_labels/round_labels');

        $this->labelCustomEnabled = $this->helper->getCfg('product_labels/custom');
        if ($this->labelCustomEnabled)
        {
            $this->customLabelAttributeCode = $this->helper->getCfg('product_labels/custom_label_attr_code');

            // If attribute does not exist, set the code to null
            try
            {
                $attribute = $this->attributeRepository->get(ProductAttributeInterface::ENTITY_TYPE_CODE, $this->customLabelAttributeCode);
            }
            catch(\Exception $e)
            {
                $this->customLabelAttributeCode = null;
            }
        }

        parent::__construct($context);
    }

    /**
     * Get classes of product labels
     *
     * @return string
     */
    public function getLabelsClasses()
    {
        if ($this->roundLabels)
        {
            return 'round-stickers';
        }

        return '';
    }

    /**
     * Get product labels HTML
     *
     * @return string
     */
    public function getLabels($product)
    {
        $html = '';

        $showNewLabel = false;
        if ($this->labelNewEnabled)
        {   
            $showNewLabel = $this->isNew($product);
        }

        $showSavedValueLabel = false;
        $savedValue = '';
        if ($this->labelSavedValueEnabled)
        {
            $savedValue = $this->_getSavedPercentage($product);
            if (!empty($savedValue))
            {
                $showSavedValueLabel = true;
            }
        }

        // The "saved value" label excludes the "sale" label, so only if it's not displayed
        // we can check if the "sale" label should be displayed.
        $showSaleLabel = false;
        if ($showSavedValueLabel === false && $this->labelSaleEnabled)
        {
            if ($this->_isSaleAttributeTrue($product) || $this->_hasSpecialPrice($product))
            {
                $showSaleLabel = true;
            }
        }

        $showCustomLabel = false;
        $customAttrValue = '';
        if (!empty($this->customLabelAttributeCode))
        {
            $customAttrValue = $this->_getCustomAttribute($product);
            if ($customAttrValue !== false)
            {
                $showCustomLabel = true;
            }
        }

        $hasAnyLabels = ($showNewLabel || $showSavedValueLabel || $showSaleLabel || $showCustomLabel) ? true : false;

        if ($hasAnyLabels)
        {
            $html .= '<span class="sticker-wrapper top-left">'; // Open wrapper
        }

        if ($showNewLabel)
        {
            $html .= '<span class="sticker new">' . __('New') . '</span>';
        }

        if ($showSavedValueLabel)
        {
            $html .= '<span class="sticker sale save">-' . $savedValue . '%</span>';
        }
        
        if ($showSaleLabel)
        {
            $html .= '<span class="sticker sale">' . __('Sale') . '</span>';
        }

        if ($showCustomLabel)
        {
            $html .= '<span class="sticker custom">' . $customAttrValue . '</span>';
        }

        if ($hasAnyLabels)
        {
            $html .= '</span>'; // Close wrapper
        }
        
        return $html;
    }
    
    /**
     * Check if product is marked as "new"
     *
     * @return bool
     */
    public function isNew($product)
    {
        // Check if product is marked as "new" by an attribute OR if date range is correct
        if ($product->getData('new') || $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date')))
        {
            return true;
        }

        return false;
    }

    /**
     * Check if product has special price and calculate saved value (percentage)
     *
     * @return string
     */
    protected function _getSavedPercentage($product)
    {
        $percentage = '';

        if ($this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date')))
        {
            $specialPrice = $product->getFinalPrice();
            $regularPrice = $product->getPrice();

            if (empty($regularPrice))
            {
                return 0;
            }

            if ($specialPrice !== $regularPrice)
            {
                if($regularPrice == 0){
                    $percentage = 0;
                }else{
                    $percentage = round((($regularPrice - $specialPrice) / $regularPrice) * 100, $this->savedValuePrecision);
                }
                $percentage = 100;
            }
        }

        return $percentage;
    }

    /**
     * Check if product has special price
     *
     * @return bool
     */
    protected function _hasSpecialPrice($product)
    {
        if ($this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date')))
        {
            $specialPrice = $product->getFinalPrice();
            $regularPrice = $product->getPrice();

            // If date range is correct, also check if final price is different than regular price
            if ($specialPrice !== $regularPrice)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if product is marked as "on sale" by an attribute
     *
     * @return bool
     */
    protected function _isSaleAttributeTrue($product)
    {
        if ($product->getData('sale'))
        {
            return true;
        }

        return false;
    }

    /**
     * Get value of a custom attribute
     *
     * @return string|bool
     */
    protected function _getCustomAttribute($product)
    {
        $value = $product->getAttributeText($this->customLabelAttributeCode);

        if ($value === false || $value === null)
        {
            return false;
        }
        elseif (is_array($value))
        {
            return implode(' ', $value);
        }
        else
        {
            return $value;
        }
    }

    /**
     * Check if now is in the date range
     *
     * @return bool
     */
    protected function _nowIsBetween($fromDate, $toDate)
    {
        if ($fromDate)
        {
            $fromDate = strtotime($fromDate);
            $toDate = strtotime($toDate);
            $now = $this->getNowBasedOnLocale->getNowTimeStamp();

            if ($toDate)
            {
                if ($fromDate <= $now && $now <= $toDate)
                    return true;
            }
            else
            {
                if ($fromDate <= $now)
                    return true;
            }
        }
        
        return false;
    }

    /**
     * @deprecated
     * Check if product is on sale
     *
     * @return bool
     */
    public function isOnSale($product)
    {
        if ($product->getData('sale') || $this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date')))
        {
            return true;
        }

        return false;
    }
}
