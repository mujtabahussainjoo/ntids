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

use \Fooman\Totals\Model\QuoteAddressTotalManagement;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var \Fooman\Surcharge\Model\SurchargeFactory
     */
    private $surchargeFactory;

    /**
     * @var QuoteAddressTotalManagement
     */
    private $quoteAddressTotalManagement;

    /**
     * @param \Magento\Backend\App\Action\Context      $context
     * @param \Fooman\Surcharge\Model\SurchargeFactory $surchargeFactory
     * @param QuoteAddressTotalManagement              $quoteAddressTotalManagement
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Fooman\Surcharge\Model\SurchargeFactory $surchargeFactory,
        QuoteAddressTotalManagement $quoteAddressTotalManagement
    ) {
        $this->surchargeFactory = $surchargeFactory;
        $this->quoteAddressTotalManagement = $quoteAddressTotalManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $surcharge = $this->surchargeFactory->create()->load($id);

            try {
                $typeId = $surcharge->getTypeId();
                $surcharge->delete();
                $this->quoteAddressTotalManagement->deleteByTypeId($typeId);
                $this->messageManager->addSuccessMessage(__('You deleted the surcharge.'));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __("You can't delete the surcharge."));
            }
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fooman_Surcharge::surcharge');
    }
}
