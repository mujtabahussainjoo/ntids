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



namespace Mirasvit\Report\Repository\Email;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Mirasvit\Report\Api\Repository\Email\BlockRepositoryInterface;
use Mirasvit\Report\Api\Repository\ReportRepositoryInterface;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\ReportApi\Api\RequestBuilderInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultRepository implements BlockRepositoryInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ReportRepositoryInterface
     */
    protected $reportRepository;

    /**
     * @var RequestInterface
     */
    protected $request;


    /**
     * @var DateServiceInterface
     */
    protected $dateService;


    private $requestBuilder;

    public function __construct(
        RequestBuilderInterface $requestBuilder,
        ReportRepositoryInterface $reportRepository,
        Registry $registry,
        RequestInterface $request,
        DateServiceInterface $dateService
    ) {
        $this->requestBuilder   = $requestBuilder;
        $this->registry         = $registry;
        $this->reportRepository = $reportRepository;
        $this->request          = $request;
        $this->dateService      = $dateService;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        $blocks = [];
        foreach ($this->reportRepository->getList() as $report) {
            if ($report->getName()) {
                $blocks[$report->getIdentifier()] = __('Report: %1', $report->getName());
            }
        }

        return $blocks;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getContent($identifier, $data)
    {
        return $this->build($data);
    }

    /**
     * @param array $reportData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function build(array $reportData)
    {
        $reportIdentifier = $reportData['identifier'];
        $report           = $this->reportRepository->get($reportIdentifier);
        $interval         = $this->dateService->getInterval($reportData['timeRange']);
        $request          = $this->requestBuilder->create()
            ->setTable($report->getTable())
            ->setDimensions($report->getDimensions())
            ->setPageSize((isset($reportData['limit']) && $reportData['limit']) ? $reportData['limit'] : 100)
            ->addFilter('sales_order|created_at', $interval->getFrom()->toString(\Zend_Date::W3C), 'gteq', 'A')
            ->addFilter('sales_order|created_at', $interval->getTo()->toString(\Zend_Date::W3C), 'lteq', 'A');

        foreach ($report->getDimensions() as $column) {
            $request->addColumn($column);
        }
        foreach ($report->getColumns() as $column) {
            $request->addColumn($column);
        }

        $response = $request->process();

        $rows = [];
        foreach ($response->getColumns() as $column) {
            $rows['header'][] = $column->getLabel();
        }

        foreach ($response->getItems() as $idx => $item) {
            foreach ($item->getFormattedData() as $key => $value) {
                $rows[$idx][] = $value;
            }
        }

        foreach ($response->getTotals()->getFormattedData() as $key => $value) {
            $rows['footer'][] = $value;
        }

        $table = '<table>';
        foreach ($rows as $idx => $row) {
            $table .= '<tr>';
            foreach ($row as $column) {
                if ($idx === 'header' || $idx === 'footer') {
                    $table .= '<th>' . $column . '</th>';
                } else {
                    $table .= '<td>' . $column . '</td>';
                }
            }
            $table .= '</tr>';
        }

        $table .= '</table>';

        $name = $report->getName();

        return "
            <h2>{$name}</h2>
            <div class='interval'>{$this->dateService->getIntervalHint($reportData['timeRange'])}</div>
            
            <div class='table-wrapper'>$table</div>
        ";
    }
}
