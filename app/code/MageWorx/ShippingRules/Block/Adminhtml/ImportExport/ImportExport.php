<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\ImportExport;

use Magento\Backend\Block\Template\Context;

/**
 * Class ImportExport
 * @package MageWorx\ShippingRules\Block\Adminhtml\ImportExport
 *
 * @method bool|null getIsReadonly()
 * @method ImportExport setUseContainer($bool)
 */
class ImportExport extends \Magento\Backend\Block\Widget
{
    /**
     * @var string
     */
    protected $_template = 'MageWorx_ShippingRules::datatransfer/import_export.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setUseContainer(true);
    }

    /**
     * Return CSS classes for the export carriers form container (<div>)
     * as a string concatenated with a space or as an array
     *
     * @param bool $asString
     * @return array|string
     */
    public function getExportCarriersClasses($asString = true)
    {
        $exportCarriersClasses = ['export-carriers'];
        if ($this->getIsReadonly()) {
            $exportCarriersClasses[] = 'box-left';
        } else {
            $exportCarriersClasses[] = 'box-right';
        }

        if ($asString) {
            $exportCarriersClasses = implode(' ', $exportCarriersClasses);
        }

        return $exportCarriersClasses;
    }
}
