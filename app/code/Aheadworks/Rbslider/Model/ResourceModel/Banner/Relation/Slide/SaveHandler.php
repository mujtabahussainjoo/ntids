<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\ResourceModel\Banner\Relation\Slide;

use Magento\Framework\App\ResourceConnection;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Rbslider\Model\ResourceModel\Banner\Relation\Slide
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
        $slideIds = json_decode($entity->getSlidePosition(), true);
        $slideIdsOrig = $this->getSlideIds($entityId);
        $connection = $this->getConnection();
        $tableName = $this->resourceConnection->getTableName('aw_rbslider_slide_banner');

        if (is_array($slideIds) && is_array($slideIdsOrig)) {
            // Slide position update
            $toUpdate = array_intersect_key($slideIds, $slideIdsOrig);
            $toUpdate = array_diff_assoc($toUpdate, $slideIdsOrig);
            if (count($toUpdate)) {
                foreach ($toUpdate as $slideId => $position) {
                    $where = ['banner_id = ?' => (int)$entityId, 'slide_id = ?' => (int)$slideId];
                    $bind = ['position' => (int)$position];
                    $connection->update($tableName, $bind, $where);
                }
            }

            $toInsert = array_diff_key($slideIds, $slideIdsOrig);
            if (count($toInsert)) {
                foreach ($toInsert as $slideId => $position) {
                    $connection->insert(
                        $tableName,
                        ['banner_id' => $entityId, 'slide_id' => $slideId, 'position' => $position]
                    );
                    $connection->insert(
                        $this->resourceConnection->getTableName('aw_rbslider_statistic'),
                        ['slide_banner_id' => $connection->lastInsertId(), 'view_count' => 0, 'click_count' => 0]
                    );
                }
            }

            $toDelete = array_diff_key($slideIdsOrig, $slideIds);
            if (count($toDelete)) {
                $connection->delete(
                    $tableName,
                    ['banner_id = ?' => $entityId, 'slide_id IN (?)' => array_keys($toDelete)]
                );
            }
        }
        return $entity;
    }

    /**
     * Get slide IDs to which entity is assigned
     *
     * @param int $entityId
     * @return array
     */
    private function getSlideIds($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('aw_rbslider_slide_banner'), ['slide_id', 'position'])
            ->where('banner_id = :id');
        return $connection->fetchPairs($select, ['id' => $entityId]);
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
            $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnectionName()
        );
    }
}
