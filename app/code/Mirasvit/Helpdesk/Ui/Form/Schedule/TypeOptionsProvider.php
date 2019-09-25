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


namespace Mirasvit\Helpdesk\Ui\Form\Schedule;

class TypeOptionsProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_ALWAYS,
                'label' => __('24 hrs x 7 days')->getText()
            ],
            [
                'value' => \Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_CUSTOM,
                'label' => __('Select working days/hours')->getText()
            ],
            [
                'value' => \Mirasvit\Helpdesk\Model\Config::SCHEDULE_TYPE_CLOSED,
                'label' => __('Closed')->getText()
            ],
        ];
    }
}
