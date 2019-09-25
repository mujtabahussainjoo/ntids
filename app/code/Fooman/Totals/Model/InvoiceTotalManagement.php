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

use Fooman\Totals\Model\ResourceModel\InvoiceTotal\CollectionFactory;

class InvoiceTotalManagement
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $typeId
     * @param int $invoiceId
     *
     * @return \Fooman\Totals\Api\Data\InvoiceTotalInterface[]
     */
    public function getByTypeAndInvoiceId($typeId, $invoiceId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\InvoiceTotal\Collection $invoiceTotalCollection */
        $invoiceTotalCollection = $this->collectionFactory->create();

        $collection = $invoiceTotalCollection
            ->addFieldToFilter('type_id', $typeId)
            ->addFieldToFilter('invoice_id', (string) $invoiceId);

        return $collection->getItems();
    }

    /**
     * @param  int    $invoiceId
     *
     * @return \Fooman\Totals\Api\Data\InvoiceTotalInterface[]
     */
    public function getByInvoiceId($invoiceId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\InvoiceTotal\Collection $invoiceTotalCollection */
        $invoiceTotalCollection = $this->collectionFactory->create();

        $collection = $invoiceTotalCollection
            ->addFieldToFilter('invoice_id', (string) $invoiceId);

        return $collection->getItems();
    }

    /**
     * @param  string    $code
     * @param  int       $invoiceId
     *
     * @return \Fooman\Totals\Api\Data\InvoiceTotalInterface[]
     */
    public function getByCodeAndInvoiceId($code, $invoiceId)
    {
        /** @var \Fooman\Totals\Model\ResourceModel\InvoiceTotal\Collection $invoiceTotalCollection */
        $invoiceTotalCollection = $this->collectionFactory->create();

        $collection = $invoiceTotalCollection
            ->addFieldToFilter('code', $code)
            ->addFieldToFilter('invoice_id', (string) $invoiceId);

        return $collection->getItems();
    }
}
