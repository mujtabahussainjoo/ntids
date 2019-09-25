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



namespace Mirasvit\Report\Model\Export;

use Magento\Framework\Convert\Excel;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Filesystem;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use Mirasvit\ReportApi\Api\ResponseInterface;

class ConvertToXml extends ConvertToCsv
{
    protected $excelFactory;

    protected $iteratorFactory;

    public function __construct(
        ExcelFactory $excelFactory,
        SearchResultIteratorFactory $iteratorFactory,
        Filesystem $filesystem
    ) {
        $this->excelFactory    = $excelFactory;
        $this->iteratorFactory = $iteratorFactory;

        parent::__construct($filesystem);
    }

    /**
     * @param \Mirasvit\ReportApi\Processor\ResponseItem $item
     * @return array
     */
    public function getItemData($item)
    {
        return $item->getFormattedData();
    }

    public function getXmlFile(ResponseInterface $response)
    {

        $name = md5(microtime());
        $file = 'export/' . $name . '.xml';

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        /** @var Excel $excel */
        $excel = $this->excelFactory->create([
            'iterator'    => $this->iteratorFactory->create(['items' => $response->getItems()]),
            'rowCallback' => [$this, 'getItemData'],
        ]);

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $header = [];
        foreach ($response->getColumns() as $column) {
            $header[] = $column->getLabel();
        }
        $excel->setDataHeader($header);

        $excel->write($stream, $name . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true  // can delete file after use
        ];
    }
}
