<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\ImportExport;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

class ExportPost extends \MageWorx\ShippingRules\Controller\Adminhtml\ImportExport
{
    /**
     * Export action from import/export shipping carriers, methods and rates
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \MageWorx\ShippingRules\Model\ImportExport\ExportHandler $exportHandler */
        $exportHandler = $this->exportHandlerFactory->create();
        $content = $exportHandler->getContent();

        return $this->fileFactory->create(
            'carriers_methods_rates_' . date('Y-m-d') . '_' . time() . '.csv',
            $content,
            DirectoryList::VAR_DIR
        );
    }
}
