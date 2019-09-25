<?php

namespace Serole\OvernightUpload\Controller\Adminhtml\Provider;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Serole\OvernightUpload\Model\ResourceModel\Providergrid\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{

    protected $_filter;


    protected $_collectionFactory;


    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            $record->setId($record->getId());
            $record->delete();
            $recordDeleted++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('grid/provider/index');
    }


    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Serole_OvernightUpload::row_data_delete');
    }
}
