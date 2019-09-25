<?php

namespace Wizkunde\WebSSO\Plugin;

use Wizkunde\WebSSO\Helper\Eav;

class AfterLoad
{
    private $mappingCollectionFactory = null;
    private $eavHelper = null;

    public function __construct(
        \Wizkunde\WebSSO\Model\ResourceModel\Mapping\CollectionFactory $mappingCollectionFactory,
        Eav $eavHelper
    ) {
        $this->eavHelper = $eavHelper;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
    }

    public function afterLoad($model)
    {
        $mappings = [];

        if ($model->getId() > 0) {
            $mappingCollection = $this->mappingCollectionFactory->create();
            $mappingCollection->addFieldToFilter('server_id', $model->getId());

            foreach ($mappingCollection->getItems() as $item) {
                $mapping = $item->getData();

                $external = unserialize($mapping['external']);
                $mapping['external'] = $external['value'];
                $mapping['transform'] = $external['transform'];
                $mapping['extra'] = $external['extra'];

                $mappings[] = $mapping;
            }

            $model->addData(
                [
                    'mappings' => $mappings,
                    'eav_attributes' => $this->eavHelper->getAvailableAttributes()
                ]
            );
        }
    }
}
