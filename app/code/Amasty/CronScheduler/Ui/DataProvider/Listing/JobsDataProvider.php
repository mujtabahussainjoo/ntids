<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CronScheduler
 */


namespace Amasty\CronScheduler\Ui\DataProvider\Listing;

class JobsDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Amasty\CronScheduler\Model\ResourceModel\Jobs\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\CronScheduler\Model\ResourceModel\Jobs\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }

    public function addOrder($field, $direction)
    {
        if ($field === 'group') {
            $field = '`group`';
        }
        parent::addOrder($field, $direction);
    }
}
