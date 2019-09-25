<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Ui\Component\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

class ArticleGridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param string                                                             $name
     * @param string                                                             $primaryFieldName
     * @param string                                                             $requestFieldName
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting
     * @param SearchCriteriaBuilder                                              $searchCriteriaBuilder
     * @param RequestInterface                                                   $request
     * @param FilterBuilder                                                      $filterBuilder
     * @param \Magento\Framework\Registry                                        $registry
     * @param array                                                              $meta
     * @param array                                                              $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->registry = $registry;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getField() == 'user_id') {
            $filter->setField('main_table.user_id');
        }
        if ($filter->getField() == 'is_active') {
            $filter->setField('main_table.is_active');
        }
        if ($filter->getField() == 'store_id') {
            $filter->setField('article_store.as_store_id');
            $filter->setConditionType('in');
            $value = (array)$filter->getValue();
            $value[] = 0;
            $filter->setValue($value);
        }
        if ($filter->getField() == 'category_id') {
            $filter->setField('article_category.ac_category_id');
        }
        if ($filter->getField() == 'created_at') {
            $filter->setField('main_table.created_at');
        }

        parent::addFilter($filter);
    }

    /**
     * Returns Search result
     *
     * @return \Mirasvit\Kb\Model\ResourceModel\Article\Collection
     */
    public function getSearchResult()
    {
        return $this->reporting->search($this->getSearchCriteria())->joinStoreIds()->joinCategoryIds();
    }
}
