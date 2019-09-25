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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Ui\Component\DataProvider;

use Magento\Framework\Data\Collection as DbCollection;
use Magento\Framework\Api\Filter;

/**
 * Class Fulltext.
 */
class FulltextFilter implements \Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface
{
    /**
     * Returns list of columns from fulltext index (doesn't support more then one FTI per table).
     *
     * @param DbCollection $collection
     * @param string       $indexTable
     *
     * @return array
     */
    protected function getFulltextIndexColumns(DbCollection $collection, $indexTable)
    {
        $indexes = $collection->getConnection()->getIndexList($indexTable);
        foreach ($indexes as $index) {
            if (strtoupper($index['INDEX_TYPE']) == 'FULLTEXT') {
                return $index['COLUMNS_LIST'];
            }
        }

        return [];
    }

    /**
     * Apply fulltext filters.
     *
     * @param DbCollection $collection
     * @param Filter       $filter
     * @return void
     */
    public function apply(DbCollection $collection, Filter $filter)
    {
        $columns = $this->getFulltextIndexColumns($collection, $collection->getMainTable());
        if (!$columns) {
            return;
        }
        $collection->getSelect()
            ->where(
                'MATCH('.implode(',', $columns).') AGAINST(?)',
                $filter->getValue()
            );
    }
}
