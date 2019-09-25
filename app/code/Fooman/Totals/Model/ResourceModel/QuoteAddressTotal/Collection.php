<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Model\ResourceModel\QuoteAddressTotal;

/**
 * @method \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[] getItems()
 */

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Fooman\Totals\Model\QuoteAddressTotal::class,
            \Fooman\Totals\Model\ResourceModel\QuoteAddressTotal::class
        );
    }
}
