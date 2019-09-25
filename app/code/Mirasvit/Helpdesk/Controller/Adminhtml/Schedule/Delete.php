<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Schedule;

class Delete extends \Mirasvit\Helpdesk\Controller\Adminhtml\Schedule
{
    /**
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $schedule = $this->scheduleFactory->create();

                $schedule->setId($this->getRequest()
                    ->getParam('id'));
                if (count($schedule->getAssignedGateways())) {
                    $this->messageManager->addError(
                        __('You can not delete this schedule, because there are gateways using it.
                         Please, change gateways settings')
                    );
                } else {
                    $schedule->delete();
                    $this->messageManager->addSuccess(
                        __('Schedule was successfully deleted')
                    );
                }
                $this->_redirect('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()
                    ->getParam('id'), ]);
            }
        }
        $this->_redirect('*/*/');
    }
}
