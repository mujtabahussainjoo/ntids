<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Statistic;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Model\ResourceModel\Statistic\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Rbslider\Api\StatisticRepositoryInterface;

/**
 * Class MassReset
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Statistic
 */
class MassReset extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Rbslider::statistics';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var StatisticRepositoryInterface
     */
    private $statisticRepository;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param StatisticRepositoryInterface $statisticRepository
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Filter $filter,
        StatisticRepositoryInterface $statisticRepository
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->statisticRepository = $statisticRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection->getItems() as $item) {
                $statisticDataObject = $this->statisticRepository->get($item->getId());
                $statisticDataObject->setViewCount(0);
                $statisticDataObject->setClickCount(0);
                $this->statisticRepository->save($statisticDataObject);
                $count++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated', $count));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
