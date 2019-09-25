<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class CreditmemoTotal implements ObserverInterface
{
    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalFactory
     */
    private $creditmemoTotalFactory;

    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalManagement
     */
    private $creditmemoTotalManagement;

    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    private $transactionFactory;

    /**
     * @param \Fooman\Totals\Model\CreditmemoTotalFactory    $creditmemoTotalFactory
     * @param \Fooman\Totals\Model\CreditmemoTotalManagement $creditmemoTotalManagement
     * @param \Fooman\Totals\Model\OrderTotalManagement      $orderTotalManagement
     * @param \Magento\Framework\DB\TransactionFactory       $transactionFactory
     */
    public function __construct(
        \Fooman\Totals\Model\CreditmemoTotalFactory $creditmemoTotalFactory,
        \Fooman\Totals\Model\CreditmemoTotalManagement $creditmemoTotalManagement,
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        $this->creditmemoTotalFactory = $creditmemoTotalFactory;
        $this->creditmemoTotalManagement = $creditmemoTotalManagement;
        $this->orderTotalManagement = $orderTotalManagement;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param EventObserver $observer
     *
     * @return void
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $this->saveCreditmemoTotals($observer->getCreditmemo());
    }

    /**
     * @param  \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     *
     * @throws \Exception
     */
    private function saveCreditmemoTotals(\Magento\Sales\Api\Data\CreditmemoInterface $creditmemo)
    {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if (!$extensionAttributes) {
            return;
        }
        $foomanCreditmemoTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanCreditmemoTotalGroup) {
            return;
        }

        $foomanCreditmemoTotals = $foomanCreditmemoTotalGroup->getItems();
        if (!empty($foomanCreditmemoTotals)) {
            $transaction = $this->transactionFactory->create();
            foreach ($foomanCreditmemoTotals as $foomanCreditmemoTotalItem) {
                $creditmemoTotals = false;
                if ($creditmemo->getEntityId()) {
                    $creditmemoTotals = $this->creditmemoTotalManagement->getByTypeAndCreditmemoId(
                        $foomanCreditmemoTotalItem->getTypeId(),
                        $creditmemo->getEntityId()
                    );
                }

                if (!empty($creditmemoTotals)) {
                    $creditmemoTotal = array_shift($creditmemoTotals);
                } else {
                    $creditmemoTotal = $this->creditmemoTotalFactory->create();
                }

                $creditmemoTotal->setAmount($foomanCreditmemoTotalItem->getAmount());
                $creditmemoTotal->setBaseAmount($foomanCreditmemoTotalItem->getBaseAmount());
                $creditmemoTotal->setTaxAmount($foomanCreditmemoTotalItem->getTaxAmount());
                $creditmemoTotal->setBaseTaxAmount($foomanCreditmemoTotalItem->getBaseTaxAmount());
                $creditmemoTotal->setLabel($foomanCreditmemoTotalItem->getLabel());
                $creditmemoTotal->setTypeId($foomanCreditmemoTotalItem->getTypeId());
                $creditmemoTotal->setCode($foomanCreditmemoTotalItem->getCode());
                $creditmemoTotal->setOrderId($creditmemo->getOrderId());
                $creditmemoTotal->setCreditmemoId($creditmemo->getEntityId());
                $transaction->addObject($creditmemoTotal);

                //Update corresponding order total
                $orderTotal = $this->getCorrespondingOrderTotal($foomanCreditmemoTotalItem, $creditmemo);
                $orderTotal->setAmountRefunded(
                    $orderTotal->getAmountRefunded() + $foomanCreditmemoTotalItem->getAmount()
                );
                $orderTotal->setBaseAmountRefunded(
                    $orderTotal->getBaseAmountRefunded() + $foomanCreditmemoTotalItem->getBaseAmount()
                );
                $transaction->addObject($orderTotal);
            }
            $transaction->save();
        }
    }

    public function getCorrespondingOrderTotal($creditmemoTotal, $creditmemo)
    {
        $orderTotals = $this->orderTotalManagement->getByTypeIdAndOrderId(
            $creditmemoTotal->getTypeId(),
            $creditmemo->getOrderId()
        );
        return array_shift($orderTotals);
    }
}
