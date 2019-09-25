<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\Zone\Condition;

/**
 * Class Address
 *
 * @method string getAttribute()
 * @method array getAttributeOption()
 */
class Address extends \MageWorx\ShippingRules\Model\Condition\AbstractAddress
{
    /**
     * Load attribute options
     *
     * @return \MageWorx\ShippingRules\Model\Zone\Condition\Address
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'postcode' => __('Shipping Postcode'),
            'region' => __('Shipping Region'),
            'region_id' => __('Shipping State/Province'),
            'country_id' => __('Shipping Country'),
        ];

        if ($this->helper->isUKSpecificPostcodeConditionsEnabled()) {
            foreach (static::UK_POST_CODE_ATTRIBUTES as $value) {
                $attributes[$value] = ucfirst(str_ireplace('uk_', '', $value)) . ' (UK)';
            }
        }

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'country_id':
            case 'region_id':
                return 'multiselect';
        }

        return 'string';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'country_id':
            case 'region_id':
                return 'multiselect';
        }

        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = $this->_directoryCountry->toOptionArray(true);
                    break;

                case 'region_id':
                    $options = $this->_directoryAllregion->toOptionArray(true);
                    break;

                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address|\Magento\Framework\Model\AbstractModel $address */
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        $this->addUKPostCodeParts($address);

        return parent::validate($address);
    }
}
