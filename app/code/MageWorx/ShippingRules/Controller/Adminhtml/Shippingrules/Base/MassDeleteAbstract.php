<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Base;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

abstract class MassDeleteAbstract extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    protected $collectionFactory;

    protected $aclResourceName;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param null $collectionFactory
     * @param string $aclResourceName
     */
    public function __construct(
        Context $context,
        Filter $filter,
        $collectionFactory = null,
        $aclResourceName = null
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->aclResourceName = $aclResourceName;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $size = $collection->getSize();
        /** @var \Magento\Framework\Model\AbstractModel[] $collection */
        foreach ($collection as $item) {
            $item->getResource()->delete($item);
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $this->messageManager
            ->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $size)
            );
        $resultRedirect = $this->resultFactory
            ->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Returns result of current user permission check on resource and privilege
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->aclResourceName);
    }
}
