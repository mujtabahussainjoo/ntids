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


namespace Mirasvit\Helpdesk\Block\Adminhtml\Ticket\View\Customer;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Customer\Controller\RegistryConstants;

class AddTicketButton extends \Magento\Backend\Block\Template implements ButtonProviderInterface
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->context  = $context;
        $this->registry = $registry;
    }
    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'id'         => 'add_ticket',
            'label'      => __('Add Ticket to the Customer'),
            'class'      => 'add',
            'on_click'   => "location.href = '" . $this->getAddUrl() . "'",
            'sort_order' => 10
        ];
    }

    /**
     * @param array $args
     * @return string
     */
    public function getAddUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);

        if ($this->getCustomerId()) {
            $params['customer_id'] = $this->getCustomerId();
            unset($params['id']);
        }

        return $this->context->getUrlBuilder()->getUrl('helpdesk/ticket/add', $params);
    }

    /**
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_query' => ['isAjax' => null]];
    }

    /**
     * @return int
     */
    private function getCustomerId()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
}
