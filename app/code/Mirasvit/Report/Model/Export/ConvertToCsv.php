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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Mirasvit\ReportApi\Api\ResponseInterface;

class ConvertToCsv
{
    protected $directory;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    public function getCsvFile(ResponseInterface $response)
    {
        $name = md5(microtime());
        $file = 'export/' . $name . '.csv';

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $header = [];
        foreach ($response->getColumns() as $column) {
            $header[] = $column->getLabel();
        }
        $stream->writeCsv($header);

        foreach ($response->getItems() as $item) {
            $stream->writeCsv($item->getFormattedData());

            foreach ($item->getItems() as $subItem) {
                $stream->writeCsv($subItem->getFormattedData());
            }
        }

        $stream->writeCsv($response->getTotals()->getFormattedData());

        $stream->unlock();
        $stream->close();

        return [
            'type'  => 'filename',
            'value' => $file,
        ];
    }
}
