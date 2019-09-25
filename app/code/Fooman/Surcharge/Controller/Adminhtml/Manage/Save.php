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

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Fooman\Surcharge\Model\SurchargeFactory
     */
    private $surchargeFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var QuoteAddressTotalManagement
     */
    private $quoteAddressTotalManagement;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Fooman\Surcharge\Model\SurchargeFactory $surchargeFactory,
        QuoteAddressTotalManagement $quoteAddressTotalManagement
    ) {
        $this->session = $context->getSession();
        $this->surchargeFactory = $surchargeFactory;
        $this->quoteAddressTotalManagement = $quoteAddressTotalManagement;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = (int)$this->getRequest()->getParam('id');
            $type = $this->getRequest()->getParam('type');

            $surcharge = $this->surchargeFactory->create();
            if ($id) {
                $surcharge->load($id);
                $type = $surcharge->getType();

                $typeId = $surcharge->getTypeId();
                if ($data['general']['is_active'] == 0) {
                    $this->quoteAddressTotalManagement->deleteByTypeId($typeId);
                }
            }

            $dataRule = $data[$type];
            $keys = [
                'apply_group_filter',
                'groups',
                'based_on',
                'apply_region_filter',
                'countries',
                'regions',
                'region_filter_address_type'
            ];
            foreach ($keys as $key) {
                if (isset($data['general'][$key])) {
                    $dataRule[$key] = $data['general'][$key];
                    unset($data['general'][$key]);
                }
            }

            $surcharge->setData($data['general']);
            $surcharge->setType($type);
            $surcharge->setDataRule(json_encode($dataRule));

            if ($id) {
                $surcharge->setId($id);
            }

            try {
                $surcharge->save();
                $this->messageManager->addSuccessMessage(__('You saved the surcharge.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->session->setSurchargeData($data);
                return $resultRedirect->setPath('*/*/', ['id' => $surcharge->getId()]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fooman_Surcharge::surcharge');
    }
}
