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
 * @package   mirasvit/module-report
 * @version   1.3.52
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Ui;

use Magento\Backend\Block\Template;
use Mirasvit\ReportApi\Api\SchemaInterface;

class SchemaDataProvider extends Template
{
    private $schema;

    public function __construct(
        SchemaInterface $schema,
        Template\Context $context
    ) {
        $this->schema = $schema;

        parent::__construct($context);
    }

    public function getConfigData()
    {
        $result = [
            'tables'  => [],
            'columns' => [],
        ];

        foreach ($this->schema->getTables() as $table) {
            $result['tables'][$table->getName()] = [
                'identifier' => $table->getName(),
                'label'      => $table->getLabel() ? $table->getLabel() : $table->getName(),
                'internal'   => $table->getLabel() ? false : true,
            ];

            foreach ($table->getColumns() as $column) {
                $group = '';
                foreach ($column->getFields() as $field) {
                    $group .= $field->getName();
                }

                $result['columns'][$column->getIdentifier()] = [
                    'label'      => $column->getLabel(),
                    'group'      => $group,
                    'identifier' => $column->getIdentifier(),
                    'table'      => $column->getTable()->getName(),
                    'type'       => $column->getType()->getType(),
                    'filterType' => $column->getType()->getJsFilterType(),
                    'aggregator' => $column->getAggregator()->getType(),
                    'options'    => method_exists($column->getType(), 'getOptions')
                        ? $column->getType()->getOptions()
                        : [],
                ];
            }
        }

        return $result;
    }

    public function toHtml()
    {
        $json = \Zend_Json::encode($this->getConfigData());

        return "<script>var schemaDataProvider = $json</script>";
    }
}