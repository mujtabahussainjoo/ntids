<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml;

abstract class ImportExport extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageWorx_ShippingRules::import_export';

    /**
     * Menu id
     */
    const MENU_IDENTIFIER = 'MageWorx_ShippingRules::system_import_export';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \MageWorx\ShippingRules\Api\ExportHandlerInterfaceFactory
     */
    protected $exportHandlerFactory;

    /**
     * @var \MageWorx\ShippingRules\Api\ImportHandlerInterfaceFactory
     */
    protected $importHandlerFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \MageWorx\ShippingRules\Api\ExportHandlerInterfaceFactory $exportHandlerFactory
     * @param \MageWorx\ShippingRules\Api\ImportHandlerInterfaceFactory $importHandlerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \MageWorx\ShippingRules\Api\ExportHandlerInterfaceFactory $exportHandlerFactory,
        \MageWorx\ShippingRules\Api\ImportHandlerInterfaceFactory $importHandlerFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->exportHandlerFactory = $exportHandlerFactory;
        $this->importHandlerFactory = $importHandlerFactory;
        parent::__construct($context);
    }
}
