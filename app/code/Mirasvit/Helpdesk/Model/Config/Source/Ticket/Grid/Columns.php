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



namespace Mirasvit\Helpdesk\Model\Config\Source\Ticket\Grid;

use Mirasvit\Helpdesk\Model\Config as Config;

class Columns implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Helper\Field  $helpdeskField
     * @param \Magento\Framework\Model\Context $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Magento\Framework\Model\Context $context
    ) {
        $this->helpdeskField = $helpdeskField;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = [
            Config::TICKET_GRID_COLUMNS_CODE => __('ID'),
            Config::TICKET_GRID_COLUMNS_NAME => __('Subject'),
            Config::TICKET_GRID_COLUMNS_CUSTOMER_NAME => __('Customer Name'),
            Config::TICKET_GRID_COLUMNS_LAST_REPLY_NAME => __('Last Replier'),
            Config::TICKET_GRID_COLUMNS_USER_ID => __('Owner'),
            Config::TICKET_GRID_COLUMNS_DEPARTMENT_ID => __('Department'),
            Config::TICKET_GRID_COLUMNS_STORE_ID => __('Store'),
            Config::TICKET_GRID_COLUMNS_STATUS_ID => __('Status'),
            Config::TICKET_GRID_COLUMNS_PRIORITY_ID => __('Priority'),
            Config::TICKET_GRID_COLUMNS_REPLY_CNT => __('Replies'),
            Config::TICKET_GRID_COLUMNS_CREATED_AT => __('Created At'),
            Config::TICKET_GRID_COLUMNS_UPDATED_AT => __('Updated At'),
            Config::TICKET_GRID_COLUMNS_LAST_REPLY_AT => __('Last Reply At'),
            Config::TICKET_GRID_COLUMNS_LAST_ACTIVITY => __('Last Reply'),
            Config::TICKET_GRID_COLUMNS_ACTION => __('View link'),
        ];

        foreach ($this->helpdeskField->getStaffCollection() as $field) {
            $options[$field->getCode()] = $field->getName();
        }

        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }
        $collection = $this->helpdeskField->getActiveCollection();
        foreach ($collection as $field) {
            $result[] = ['value' => $field->getCode(), 'label' => $field->getName()];
        }

        return $result;
    }
}
