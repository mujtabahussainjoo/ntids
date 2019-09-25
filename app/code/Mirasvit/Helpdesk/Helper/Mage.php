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

class Mage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                      $context
     * @param \Magento\Backend\Model\Url                                 $backendUrlManager
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Url $backendUrlManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->context = $context;
        $this->backendUrlManager = $backendUrlManager;
        parent::__construct($context);
    }

    /**
     * @param int $customerId
     *
     * @return string
     */
    public function getBackendCustomerUrl($customerId)
    {
        return $this->backendUrlManager->getUrl('customer/index/edit', ['id' => $customerId]);
    }

    /**
     * @param int $orderId
     *
     * @return string
     */
    public function getBackendOrderUrl($orderId)
    {
        return $this->backendUrlManager->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    /**
     * @return $this
     */
    public function getOrderCollection()
    {
        $collection = $this->orderCollectionFactory->create()
            ->setOrder('entity_id');

        return $collection;
    }
}
