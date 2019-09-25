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



namespace Mirasvit\Helpdesk\Block\Ticket\View\Summary;

use Magento\Framework\View\Element\Template;
use Mirasvit\Helpdesk\Api\Data\TicketInterface;

class Order extends DefaultRow
{
    /**
     * @var Template\Context
     */
    private $context;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Order
     */
    private $orderHelper;

    public function __construct(
        Template\Context $context,
        \Mirasvit\Helpdesk\Helper\Order $orderHelper,
        array $data = []
    ) {
        $this->context = $context;
        $this->orderHelper = $orderHelper;

        parent::__construct($context, $data);
    }

    /**
     * @param int $orderId
     * @return string
     */
    public function getOrderUrl($orderId)
    {
        return $this->context->getUrlBuilder()->getUrl('sales/order/view', ['order_id' => $orderId]);
    }


    /**
     * @param \Magento\Sales\Model\Order|int $order
     * @param bool|string                    $url
     *
     * @return string
     */
    public function getOrderLabel($order, $url = false)
    {
        return $this->orderHelper->getOrderLabel($order, $url);
    }
}