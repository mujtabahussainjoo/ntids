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



namespace Mirasvit\Helpdesk\Helper;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Mage
     */
    protected $helpdeskMage;

    /**
     * @param \Magento\Sales\Model\Order\Status                          $orderStatus
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface       $localeDate
     * @param \Magento\Sales\Model\OrderFactory                          $orderFactory
     * @param \Mirasvit\Helpdesk\Helper\Mage                             $helpdeskMage
     * @param \Magento\Framework\App\Helper\Context                      $context
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status $orderStatus,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Mirasvit\Helpdesk\Helper\Mage $helpdeskMage,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->orderStatus = $orderStatus;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->localeDate = $localeDate;
        $this->helpdeskMage = $helpdeskMage;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Sales\Model\Order|int $order
     * @param bool|string                    $url
     *
     * @return string
     */
    public function getOrderLabel($order, $url = false)
    {
        if (!is_object($order)) {
            $order = $this->orderFactory->create()->load($order);
        }
        $res = "#{$order->getRealOrderId()}";
        if ($url) {
            $res = "<a href='{$url}' target='_blank'>$res</a>";
        }
        $status = $this->orderStatus->load($order->getStatus());
        $res .= __(
            ' at %1 (%2) - %3',
            $this->localeDate->formatDate($order->getCreatedAt(), \IntlDateFormatter::MEDIUM),
            strip_tags($order->formatPrice($order->getGrandTotal())),
            __(ucwords($status->getStoreLabel()))
        );

        return $res;
    }

    /**
     * Returns array of orders for customer
     * by customer email or id.
     *
     * @param string $customerEmail
     * @param bool   $customerId
     *
     * @return array
     */
    public function getOrderArray($customerEmail, $customerId = false)
    {
        $orders = [];
        $collection = $this->orderCollectionFactory->create();
        $collection
            ->addAttributeToSelect('*')
            ->setOrder('created_at', 'desc');
        if ($customerId) {
            $collection->addFieldToFilter(
                ['customer_email', 'customer_id'],
                [$customerEmail, $customerId]
            );
        } else {
            $collection->addFieldToFilter('customer_email', $customerEmail);
        }
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $orders[] = [
                'id' => $order->getId(),
                'name' => $this->getOrderLabel($order),
                'label' => $this->getOrderLabel($order),
                'url' => $this->helpdeskMage->getBackendOrderUrl($order->getId()),
            ];
        }

        return $orders;
    }
}
