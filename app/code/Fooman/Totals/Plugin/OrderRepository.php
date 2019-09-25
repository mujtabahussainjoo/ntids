<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Plugin;

class OrderRepository
{
    /**
     * @var \Fooman\Totals\Model\OrderTotalFactory
     */
    private $orderTotalFactory;

    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $orderTotalGroupFactory;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;

    /**
     * @param \Fooman\Totals\Model\OrderTotalFactory        $orderTotalFactory
     * @param \Fooman\Totals\Model\OrderTotalManagement     $orderTotalManagement
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \Fooman\Totals\Model\GroupFactory             $orderTotalGroupFactory
     * @param \Magento\Framework\DB\TransactionFactory      $transactionFactory
     */
    public function __construct(
        \Fooman\Totals\Model\OrderTotalFactory $orderTotalFactory,
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Fooman\Totals\Model\GroupFactory $orderTotalGroupFactory,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        $this->orderTotalFactory = $orderTotalFactory;
        $this->orderTotalManagement = $orderTotalManagement;
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderTotalGroupFactory = $orderTotalGroupFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param  \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param  \Magento\Sales\Api\Data\OrderInterface      $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface      $order
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Exception
     */
    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $this->saveOrderTotals($order);
        return $order;
    }

    /**
     * @param  \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @throws \Exception
     */
    private function saveOrderTotals(\Magento\Sales\Api\Data\OrderInterface $order)
    {

        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            return;
        }
        $foomanOrderTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanOrderTotalGroup) {
            return;
        }

        $foomanOrderTotals = $foomanOrderTotalGroup->getItems();
        if (!empty($foomanOrderTotals)) {
            $transaction = $this->transactionFactory->create();
            foreach ($foomanOrderTotals as $foomanOrderTotalItem) {
                $orderTotals = $this->orderTotalManagement->getByTypeIdAndOrderId(
                    $foomanOrderTotalItem->getTypeId(),
                    $order->getEntityId()
                );

                if (!empty($orderTotals)) {
                    $orderTotal = array_shift($orderTotals);
                } else {
                    $orderTotal = $this->orderTotalFactory->create();
                }

                $orderTotal->setAmount($foomanOrderTotalItem->getAmount());
                $orderTotal->setBaseAmount($foomanOrderTotalItem->getBaseAmount());
                $orderTotal->setTaxAmount($foomanOrderTotalItem->getTaxAmount());
                $orderTotal->setBaseTaxAmount($foomanOrderTotalItem->getBaseTaxAmount());
                $orderTotal->setAmountInvoiced($foomanOrderTotalItem->getAmountInvoiced());
                $orderTotal->setBaseAmountInvoiced($foomanOrderTotalItem->getBaseAmountInvoiced());
                $orderTotal->setAmountRefunded($foomanOrderTotalItem->getAmountRefunded());
                $orderTotal->setBaseAmountRefunded($foomanOrderTotalItem->getBaseAmountRefunded());
                $orderTotal->setLabel($foomanOrderTotalItem->getLabel());
                $orderTotal->setTypeId($foomanOrderTotalItem->getTypeId());
                $orderTotal->setCode($foomanOrderTotalItem->getCode());
                $orderTotal->setOrderId($order->getEntityId());
                $transaction->addObject($orderTotal);
            }
            $transaction->save();
        }
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface      $order
     *
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {

        $this->applyExtensionAttributes($order);
        return $order;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface        $subject
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $result
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $result
    ) {

        $orders = $result->getItems();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $this->applyExtensionAttributes($order);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     *
     * @return void
     */
    private function applyExtensionAttributes(\Magento\Sales\Api\Data\OrderInterface $order)
    {

        $extensionAttributes = $order->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            $foomanTotalGroup = $this->orderTotalGroupFactory->create();
        }

        $orderTotals = $this->orderTotalManagement->getByOrderId(
            $order->getEntityId()
        );

        if (!empty($orderTotals)) {
            foreach ($orderTotals as $orderTotal) {
                $foomanTotalGroup->addItem($orderTotal);
            }
        }
        $extensionAttributes->setFoomanTotalGroup($foomanTotalGroup);

        $order->setExtensionAttributes($extensionAttributes);
    }
}
