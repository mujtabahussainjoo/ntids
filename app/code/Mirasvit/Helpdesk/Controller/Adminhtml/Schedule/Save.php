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

use Mirasvit\Helpdesk\Api\Data\ScheduleInterface;

class Save extends \Mirasvit\Helpdesk\Controller\Adminhtml\Schedule
{
    /**
     *
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getParams()) {
            $schedule = $this->_initSchedule();

            $data['working_hours'] = $this->prepareSchedule($data);
            if (!$data['working_hours']) {
                $this->messageManager->addErrorMessage(__('Working days/hours is a required field.'));
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
            unset($data['working_time'], $data['working_time_minute'], $data['working_time_day']);

            $schedule->addData($this->prepareData($data));

            try {
                $schedule->save();

                $this->messageManager->addSuccessMessage(__('Schedule was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect(
                        '*/*/edit', ['id' => $schedule->getId(), 'store' => $schedule->getStoreId()]
                    );

                    return;
                }
                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);

                return;
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find Schedule to save'));
        $this->_redirect('*/*/');
    }

    /**
     * @param array $data
     * @return bool|string
     */
    protected function prepareSchedule($data)
    {
        $schedule = [];
        if ($data['type'] == \Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_CUSTOM) {
            if (empty($data['working_time_day'])) {
                return false;
            }
            foreach ($data['working_time_day'] as $day => $value) {
                if ($value == 1) {
                    $schedule[$day] = [
                        'from' => $data['working_time'][$day]['time_from'][0],
                        'to' => $data['working_time'][$day]['time_to'][0],
                    ];
                }
            }
        }
        return serialize($schedule);
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        if (empty($data[ScheduleInterface::ID])) {
            unset($data[ScheduleInterface::ID]);
            unset($data['id']);
        }

        return $data;
    }
}
