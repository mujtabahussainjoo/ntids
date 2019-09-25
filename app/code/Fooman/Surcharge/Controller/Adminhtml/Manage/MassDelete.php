<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Controller\Adminhtml\Manage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Fooman\Surcharge\Model\ResourceModel\Surcharge\CollectionFactory;
use \Fooman\Totals\Model\QuoteAddressTotalManagement;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /**
     * @var \Fooman\Surcharge\Model\ResourceModel\Surcharge\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var QuoteAddressTotalManagement
     */
    private $quoteAddressTotalManagement;

    /**
     * @param Context                     $context
     * @param Filter                      $filter
     * @param CollectionFactory           $collectionFactory
     * @param QuoteAddressTotalManagement $quoteAddressTotalManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        QuoteAddressTotalManagement $quoteAddressTotalManagement
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->quoteAddressTotalManagement = $quoteAddressTotalManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $surchargesDeleted = 0;
        foreach ($collection->getItems() as $surcharge) {
            $typeId = $surcharge->getTypeId();
            $this->quoteAddressTotalManagement->deleteByTypeId($typeId);
            $surcharge->delete();
            $surchargesDeleted++;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $surchargesDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fooman_Surcharge::surcharge');
    }
}
