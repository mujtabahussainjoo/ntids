<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Amasty\AdminActionsLog\Model\ResourceModel\Log\Collection;
use Amasty\AdminActionsLog\Model\ResourceModel\LogDetails\Collection as DetailsCollection;

class HistoryDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DetailsCollection
     */
    protected $detailsCollection;

    /**
     * HistoryDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RequestInterface $request
     * @param Collection $collection
     * @param DetailsCollection $detailsCollection
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        Collection $collection,
        DetailsCollection $detailsCollection,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->request = $request;
        $this->detailsCollection = $detailsCollection;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $elementId = $this->request->getParam('current_product_id');
        $this->collection->getSelect()
            ->joinLeft(
                [
                    'r' => $this->detailsCollection->getMainTable()
                ],
                'main_table.id = r.log_id',
                [
                    'is_logged' => 'MAX(r.log_id)'
                ]
            )
            ->where("element_id = ?", $elementId)
            ->where("category = ?", 'catalog/product')
            ->group('r.log_id');

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
}
