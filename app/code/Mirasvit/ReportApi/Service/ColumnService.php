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
 * @package   mirasvit/module-report-api
 * @version   1.0.16
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\ReportApi\Service;

use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\SchemaInterface;
use Mirasvit\ReportApi\Api\Service\ColumnServiceInterface;

class ColumnService implements ColumnServiceInterface
{
    private $schema;

    private $selectService;

    public function __construct(
        SchemaInterface $schema,
        SelectService $selectService
    ) {
        $this->schema        = $schema;
        $this->selectService = $selectService;
    }

    public function getApplicableDimensions(array $dimensions)
    {
        $result = [];

        foreach ($dimensions as $dimension) {
            $dimensionColumn = $this->schema->getColumn($dimension);

            foreach ($this->schema->getTables() as $table) {
                try {
                    //                    $this->selectService->getRelationType($dimensionColumn->getTable(), $table);

                    foreach ($table->getColumns() as $column) {
                        if (!$this->isAggregator($column->getAggregator())
                            && in_array($column->getType()->getType(), ['select', 'str', 'date', 'store', 'pk', 'fk', 'country'])) {
                            $result[$column->getIdentifier()] = $column->getIdentifier();
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return array_values($result);
    }

    public function getApplicableColumns(array $dimensions)
    {
        $result = [];

        foreach ($dimensions as $dimension) {
            $dimensionColumn = $this->schema->getColumn($dimension);

            foreach ($this->schema->getTables() as $table) {
                try {
                    //                    $this->selectService->getRelationType($dimensionColumn->getTable(), $table);

                    foreach ($table->getColumns() as $column) {
                        if ($this->isAggregator($column->getAggregator())) {
                            $result[$column->getIdentifier()] = $column->getIdentifier();
                        }
                    }
                } catch (\Exception $e) {

                }
            }
        }

        return array_values($result);
    }

    private function isAggregator(AggregatorInterface $aggregator)
    {
        return in_array($aggregator->getType(), [
            AggregatorInterface::TYPE_SUM,
            AggregatorInterface::TYPE_AVERAGE,
            AggregatorInterface::TYPE_COUNT,
            AggregatorInterface::TYPE_CONCAT,
        ]);
    }
}