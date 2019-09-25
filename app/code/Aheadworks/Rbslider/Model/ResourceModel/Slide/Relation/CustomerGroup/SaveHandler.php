<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel\Slide\Relation\CustomerGroup;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Rbslider\Model\ResourceModel\Slide\Relation\CustomerGroup
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $customerGroupIds = $entity->getCustomerGroupIds();

        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_rbslider_slide_customer_group');

        $connection->delete(
            $tableName,
            ['slide_id = ?' => $entityId]
        );

        $data = [];
        foreach ($customerGroupIds as $customerGroupId) {
            $data[] = [
                'slide_id' => (int)$entityId,
                'customer_group_id' => (int)$customerGroupId,
                ];
        }

        if (!empty($data)) {
            $connection->insertMultiple($tableName, $data);
        }

        return $entity;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(SlideInterface::class)->getEntityConnectionName()
        );
    }
}
