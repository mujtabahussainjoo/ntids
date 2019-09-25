<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_LayeredNavigation
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\LayeredNavigation\Plugin\Model\Adapter;

/**
 * Class Preprocessor
 * @package Mageplaza\LayeredNavigation\Model\Plugin\Adapter
 */
class Preprocessor
{
    /**
     * @type \Mageplaza\LayeredNavigation\Helper\Data
     */
    protected $_moduleHelper;

    /**
     * @param \Mageplaza\LayeredNavigation\Helper\Data $moduleHelper
     */
    public function __construct(
        \Mageplaza\LayeredNavigation\Helper\Data $moduleHelper
    ) {
    
        $this->_moduleHelper   = $moduleHelper;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject
     * @param \Closure $proceed
     * @param $filter
     * @param $isNegation
     * @param $query
     * @return string
     */
    public function aroundProcess(\Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject, \Closure $proceed, $filter, $isNegation, $query)
    {
        if ($this->_moduleHelper->isEnabled() && ($filter->getField() === 'category_ids')) {
            return 'category_ids_index.category_id IN (' . $filter->getValue() . ')';
        }

        return $proceed($filter, $isNegation, $query);
    }
}
