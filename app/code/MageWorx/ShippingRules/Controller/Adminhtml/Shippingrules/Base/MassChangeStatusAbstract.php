<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Base;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

abstract class MassChangeStatusAbstract extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';
    
    protected $collectionFactory;
    
    protected $entityFactory;

    /**
     * @var string
     */
    protected $aclResourceName;

    /**
     * @var string
     */
    protected $activeFieldName;

    /**
     * @var string
     */
    protected $activeRequestParamName;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param null $collectionFactory
     * @param null $entityFactory
     * @param string $aclResourceName
     * @param string $activeFieldName
     * @param null $activeRequestParamName
     */
    public function __construct(
        Context $context,
        Filter $filter,
        $collectionFactory = null,
        $entityFactory = null,
        $aclResourceName = null,
        $activeFieldName = null,
        $activeRequestParamName = null
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->aclResourceName = $aclResourceName;
        $this->collectionFactory = $this->_objectManager->get($collectionFactory['instance']);
        $this->entityFactory = $this->_objectManager->get($entityFactory['instance']);
        $this->activeFieldName = $activeFieldName;
        $this->activeRequestParamName = $activeRequestParamName;
    }

    /**
     * Update is active status
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $updatedCount = 0;
            foreach ($collection->getAllIds() as $entityId) {
                /** @var \Magento\Framework\Model\AbstractModel $entity */
                $entity = $this->entityFactory->create()
                    ->load($entityId);
                $entity->setData($this->activeFieldName, $this->getRequest()->getParam($this->activeRequestParamName));
                $entity->getResource()->save($entity);
                $updatedCount++;
            }

            if ($updatedCount) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $updatedCount));
            }

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);

            return $resultRedirect;
        } catch (\Exception $e) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory
                ->create(ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
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
