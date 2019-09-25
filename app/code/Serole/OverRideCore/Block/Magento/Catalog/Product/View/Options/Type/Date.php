<?php

namespace Serole\OverRideCore\Block\Magento\Catalog\Product\View\Options\Type;


class Date extends \Magento\Catalog\Block\Product\View\Options\Type\Date
{
    protected function _getSelectFromToHtml($name, $from, $to, $value = null)
    {
        $options = [['value' => '', 'label' => $name]];
        for ($i = $from; $i <= $to; $i++) {
            $options[] = ['value' => $i, 'label' => $this->_getValueWithLeadingZeros($i)];
        }
        return $this->_getHtmlSelect($name, $value)->setOptions($options)->getHtml();
    }	
	
}
	
	