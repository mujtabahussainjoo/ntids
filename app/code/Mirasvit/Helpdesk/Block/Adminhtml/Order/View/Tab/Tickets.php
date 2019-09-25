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


namespace Mirasvit\Helpdesk\Block\Adminhtml\Order\View\Tab;

class Tickets extends \Magento\Framework\View\Element\Text\ListText implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function __construct(
        \Mirasvit\Helpdesk\Api\Service\Order\OrderManagementInterface $orderManagement,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->orderManagement = $orderManagement;
        $this->registry        = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Help Desk Tickets (%1)', $this->orderManagement->getTicketsAmount($this->getOrder()));
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Help Desk Tickets (%1)', $this->orderManagement->getTicketsAmount($this->getOrder()));
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }
}
