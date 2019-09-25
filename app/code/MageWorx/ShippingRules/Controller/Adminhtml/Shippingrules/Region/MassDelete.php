<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Region;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Base\MassDeleteAbstract as BaseMassDelete;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends BaseMassDelete
{
    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param \MageWorx\ShippingRules\Model\ResourceModel\Region\Filter\CollectionFactory $collectionFactory
     * @param string $aclResourceName
     */
    public function __construct(
        Context $context,
        Filter $filter,
        \MageWorx\ShippingRules\Model\ResourceModel\Region\Filter\CollectionFactory $collectionFactory,
        $aclResourceName = 'MageWorx_ShippingRules::carrier'
    ) {
        parent::__construct($context, $filter, $collectionFactory, $aclResourceName);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        $totalDeactivated = 0;
        $totalDeleted = 0;
        /** @var \MageWorx\ShippingRules\Model\Region[] $collection */
        foreach ($collection as $item) {
            if ($item->getIsCustom()) {
                $totalDeleted++;
            } else {
                $totalDeactivated++;
            }
            $item->getResource()->delete($item);
        }

        if ($totalDeactivated > 0) {
            $this->messageManager
                ->addSuccessMessage(
                    __('A total of %1 record(s) have been deactivated.', $totalDeactivated)
                );
        }

        if ($totalDeleted > 0) {
            $this->messageManager
                ->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $totalDeleted)
                );
        }

        if (($totalDeleted + $totalDeactivated) < 0) {
            $this->messageManager
                ->addSuccessMessage(
                    __('Something went wrong, nothing was deleted.')
                );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
