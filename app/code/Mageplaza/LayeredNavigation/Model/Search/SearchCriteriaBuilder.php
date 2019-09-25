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

namespace Mageplaza\LayeredNavigation\Model\Search;

use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\Api\Search\SearchCriteriaBuilder as SourceSearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\Search\SearchCriteria;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends SourceSearchCriteriaBuilder
{
    
    protected $helper;
    
    /**
     * @param ObjectFactory $objectFactory
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        \Mageplaza\LayeredNavigation\Helper\Data $helper,
        ObjectFactory $objectFactory,
        FilterGroupBuilder $filterGroupBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->helper = $helper;
        parent::__construct($objectFactory, $filterGroupBuilder, $sortOrderBuilder);
    }

    /**
     * @param $attributeCode
     *
     * @return $this
     */
    public function removeFilter($attributeCode)
    {
        $this->filterGroupBuilder->removeFilter($attributeCode);

        return $this;
    }

    /**
     * @return SearchCriteriaBuilder
     */
    public function cloneObject()
    {
        $cloneObject = clone $this;
        $cloneObject->setFilterGroupBuilder($this->filterGroupBuilder->cloneObject());

        return $cloneObject;
    }

    /**
     * @param $filterGroupBuilder
     */
    public function setFilterGroupBuilder($filterGroupBuilder)
    {
        $this->filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * Return the Data type class name
     *
     * @return string
     */
    protected function _getDataObjectType()
    {
        return 'Magento\Framework\Api\Search\SearchCriteria';
    }
    /**
	 * Builds the SearchCriteria Data Object
	 *
	 * @return SearchCriteria
	 */
	public function create()
	{
	    if($this->helper->versionCompare('2.2.0')) {
			$this->data[SearchCriteria::FILTER_GROUPS] = [$this->filterGroupBuilder->create()];
			$this->data[SearchCriteria::SORT_ORDERS]   = [$this->sortOrderBuilder->create()];
		}
		return parent::create();
	}

	/**
	 * Create a filter group based on the filter array provided and add to the filter groups
	 *
	 * @param \Magento\Framework\Api\Filter $filter
	 * @return $this
	 */
	public function addFilter(\Magento\Framework\Api\Filter $filter)
	{
		$this->filterGroupBuilder->addFilter($filter);
		return $this;
	}
}
