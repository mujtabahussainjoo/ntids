<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Model;

use Fooman\Totals\Model\ResourceModel\QuoteAddressTotal\CollectionFactory;

class QuoteAddressTotalManagement
{

    /**
     * @var CollectionFactory
     */
    private $quoteAddressTotalCollectionFactory;

    /**
     * @param CollectionFactory $quoteAddressTotalCollectionFactory
     */
    public function __construct(
        CollectionFactory $quoteAddressTotalCollectionFactory
    ) {
        $this->quoteAddressTotalCollectionFactory = $quoteAddressTotalCollectionFactory;
    }

    /**
     * convenience helper to instantiate new Collection
     *
     * @return \Fooman\Totals\Model\ResourceModel\QuoteAddressTotal\Collection
     */
    private function getFreshCollection()
    {
        return $this->quoteAddressTotalCollectionFactory->create();
    }

    /**
     * @param int $quoteId
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function getByQuoteId($quoteId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('quote_id', ['eq' => $quoteId]);

        return $collection->getItems();
    }

    /**
     * @param int $addressId
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function getByQuoteAddressId($addressId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('quote_address_id', ['eq' => $addressId]);

        return $collection->getItems();
    }

    /**
     * @param string $code
     * @param int $quoteId
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function getByCodeAndQuoteId($code, $quoteId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('code', ['eq' => $code]);
        $collection->addFieldToFilter('quote_id', ['eq' => $quoteId]);

        return $collection->getItems();
    }

    /**
     * @param string $code
     * @param int $addressId
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function getByCodeAndQuoteAddressId($code, $addressId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('code', ['eq' => $code]);
        $collection->addFieldToFilter('quote_address_id', ['eq' => $addressId]);

        return $collection->getItems();
    }

    /**
     * @param string $typeId
     * @param int $addressId
     *
     * @return \Fooman\Totals\Api\Data\QuoteAddressTotalInterface[]
     */
    public function getByTypeIdAndAddressId($typeId, $addressId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('type_id', $typeId)
            ->addFieldToFilter('quote_address_id', (string) $addressId);

        return $collection->getItems();
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function deleteById($id)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('entity_id', ['eq' => $id]);
        $collection->walk('delete');
    }

    /**
     * @param string $typeId
     *
     * @return void
     */
    public function deleteByTypeId($typeId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('type_id', ['eq' => $typeId]);
        $collection->walk('delete');
    }

    /**
     * @param string $typeId
     * @param int $quoteId
     *
     * @return void
     */
    public function deleteByTypeIdAndQuoteId($typeId, $quoteId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('type_id', ['eq' => $typeId]);
        $collection->addFieldToFilter('quote_id', ['eq' => $quoteId]);
        $collection->walk('delete');
    }

    /**
     * @param string $code
     * @param int $quoteId
     *
     * @return void
     */
    public function deleteByCodeAndQuoteId($code, $quoteId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('code', ['eq' => $code]);
        $collection->addFieldToFilter('quote_id', ['eq' => $quoteId]);

        $collection->walk('delete');
    }

    /**
     * @param string $code
     * @param int $addressId
     *
     * @return void
     */
    public function deleteByCodeAndQuoteAddressId($code, $addressId)
    {
        $collection = $this->getFreshCollection();
        $collection->addFieldToFilter('code', ['eq' => $code]);
        $collection->addFieldToFilter('quote_address_id', ['eq' => $addressId]);

        $collection->walk('delete');
    }
}
