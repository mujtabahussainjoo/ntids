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


namespace Mirasvit\Helpdesk\Ui\Component\Listing\Columns;

use Mirasvit\Helpdesk\Model\ScheduleFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class WorkingHours extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function __construct(
        ScheduleFactory $scheduleFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $config = $this->getConfiguration();
        if (!isset($config['columnName'])) {
            return $dataSource;
        }
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($this->getData('name') == $config['columnName']) {
                    $item[$this->getData('name')] = $this->prepareItem($item[$config['idColumnName']]);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param int $id
     * @return string
     */
    protected function prepareItem($id)
    {
        $html = '';

        $schedule = $this->scheduleFactory->create();
        $this->scheduleFactory->create()->getResource()->load($schedule, $id);

        foreach ($schedule->getWorkingHours() as $day) {
            $day->setType($schedule->getType());
            $html .= '<div>';
            $html .= '<span class="schedule-day-block">' . $day->getWeekdayLocalized() . ': </span>';
            $html .= '<span class="schedule-time-block">' . $day->getWorkingTime() . '</span>';
            $html .= '</div>';
        }
        $html .= '<div class="schedule-timezone-block">' . $schedule->getTimezoneOffset() . '</div>';

        return $html;
    }
}
