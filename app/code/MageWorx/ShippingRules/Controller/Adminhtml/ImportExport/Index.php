<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\ImportExport;

use Magento\Framework\Controller\ResultFactory;

class Index extends \MageWorx\ShippingRules\Controller\Adminhtml\ImportExport
{
    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu(static::MENU_IDENTIFIER);

        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\ShippingRules\Block\Adminhtml\ImportExport\ImportExportHeader::class
            )
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(
                \MageWorx\ShippingRules\Block\Adminhtml\ImportExport\ImportExport::class
            )
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Carriers, Methods and Rates'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Shipping Carriers, Methods and Rates'));

        return $resultPage;
    }
}
