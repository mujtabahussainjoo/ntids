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
use Magento\Framework\Registry;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Mirasvit\Report\Service\StateService;
use Mirasvit\ReportApi\Api\Service\ColumnServiceInterface;

class ReportDataProvider extends Template
{
    private $reportRepository;

    private $columnService;

    private $stateService;

    private $registry;

    private $urlBuilder;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ColumnServiceInterface $columnService,
        StateService $stateService,
        Registry $registry,
        Template\Context $context
    ) {
        $this->reportRepository = $reportRepository;
        $this->columnService    = $columnService;
        $this->stateService     = $stateService;
        $this->registry         = $registry;
        $this->urlBuilder       = $context->getUrlBuilder();

        parent::__construct($context);
    }

    public function getConfigData()
    {
        $currentReport = $this->getReport();

        if (!$currentReport) {
            return null;
        }

        $result = [
            'report'     => $currentReport->getIdentifier(),
            'reports'    => [],
            'requestUrl' => $this->getApiRequestUrl(),
            'stateUrl'   => $this->getApiStateUrl(),
            'exportUrl'  => $this->getApiExportUrl(),
        ];

        foreach ($this->reportRepository->getList() as $report) {
            if ($report->getIdentifier() !== $currentReport->getIdentifier()) {
                continue;
            }

            $report->init();

            $state = [
                'identifier'  => $report->getIdentifier(),
                'table'       => $report->getTable(),
                'dimensions'  => $report->getDimensions(),
                'columns'     => $report->getColumns(),
                'filters'     => [],
                'sortOrders'  => [],
                'currentPage' => 1,
                'pageSize'    => 20,

                'chartType'    => $report->getChartConfig()->getType(),
                'chartColumns' => $report->getChartConfig()->getDefaultColumns(),
            ];

            $schema = [
                'primaryFilters'       => $report->getPrimaryFilters(),
                'primaryDimensions'    => $report->getPrimaryDimensions(),
                'applicableDimensions' => $this->columnService
                    ->getApplicableDimensions($report->getPrimaryDimensions()),
                'applicableColumns'    => $this->columnService
                    ->getApplicableColumns($report->getPrimaryDimensions()),
                'internalColumns'      => $report->getInternalColumns(),
                'internalFilters'      => $report->getInternalFilters(),
            ];

            $result['reports'][$report->getIdentifier()] = [
                'identifier' => $report->getIdentifier(),
                'name'       => $report->getName(),
                'state'      => $state,
                'schema'     => $schema,
            ];
        }

        return $result;
    }

    /**
     * @return ReportInterface
     */
    private function getReport()
    {
        return $this->registry->registry('current_report');
    }

    public function getApiRequestUrl()
    {
        return $this->urlBuilder->getUrl('report/api/request');
    }

    public function getApiStateUrl()
    {
        return $this->urlBuilder->getUrl('report/api/state');
    }

    public function getApiExportUrl()
    {
        return $this->urlBuilder->getUrl('report/api/export');
    }

    public function toHtml()
    {
        $json = \Zend_Json::encode($this->getConfigData());

        return "<script>var reportDataProvider = $json</script>";
    }
}