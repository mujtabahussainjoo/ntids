<?php

namespace Serole\Pdf\Observer;

use Magento\Framework\Event\ObserverInterface;


class Creditmemo implements ObserverInterface {

    protected $orderPdf;

    protected $order;

    protected $messageManager;

    public function __construct(\Serole\Pdf\Model\Pdf $orderPdf,
                                \Magento\Framework\Message\ManagerInterface $messageManager,
                                \Magento\Sales\Model\Order $order

    ) {
        $this->orderPdf = $orderPdf;
        $this->order = $order;
        $this->messageManager = $messageManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/observer-creditmemo-serialcodes-release.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            $creditMemoQty = 0;
            $orderQty = 0;
            $creditMemo = $observer->getEvent()->getCreditmemo();
            $orderId = $creditMemo->getOrderId();
            $orderObj = $this->order->load($orderId);
            $incrementId = $orderObj->getIncrementId();
            $orderItems = $orderObj->getAllItems();

            foreach ($creditMemo->getAllItems() as $item) {
                $creditMemoQty += $item->getQty();
            }

            foreach ($orderItems as $orderItem) {
                $orderQty += $orderItem->getQtyOrdered();
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $tableName = $resource->getTableName('order_item_serialcode');
            $fetchQuery = "select * from " . $tableName . " where OrderId =" . $incrementId;
            $fetchResult = $connection->fetchAll($fetchQuery);

            $logger->info($fetchResult);

            if(!empty($fetchResult)) {
                    if ($creditMemoQty === $orderQty) {
                      $sql = "update " . $tableName . " set status = 0 where OrderId =" . $incrementId;
                      $logger->info($sql);
                      $connection->query($sql);
                } else {
                    if (!empty($fetchResult)) {
                        $this->messageManager->addWarningMessage("Please release serialcode for this order from 'Sales' Menu => Magnage Serial Codes");
                    }
                }
            }
        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }
    }

}

