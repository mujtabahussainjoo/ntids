<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Helper;

class Order
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\OrderTotalFactory
     */
    private $orderTotalFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
     * @param \Fooman\Totals\Model\OrderTotalFactory        $orderTotalFactory
     * @param \Fooman\Totals\Model\GroupFactory             $groupFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory,
        \Fooman\Totals\Model\OrderTotalFactory $orderTotalFactory,
        \Fooman\Totals\Model\GroupFactory $groupFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->orderTotalFactory = $orderTotalFactory;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order                  $order
     * @param \Fooman\Totals\Api\Data\OrderTotalInterface $total
     */
    public function setExtensionAttributes(
        \Magento\Sales\Model\Order $order,
        \Fooman\Totals\Api\Data\OrderTotalInterface $total
    ) {
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\TotalGroupInterface $orderTotalGroup */
        $orderTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$orderTotalGroup) {
            $orderTotalGroup = $this->groupFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\OrderTotalInterface $orderTotal */
        $orderTotal = $orderTotalGroup->getByTypeId($total->getTypeId());

        if (!$orderTotal) {
            $orderTotal = $this->orderTotalFactory->create();
        }

        $orderTotal->setAmount($total->getAmount());
        $orderTotal->setBaseAmount($total->getBaseAmount());
        $orderTotal->setTaxAmount($total->getTaxAmount());
        $orderTotal->setBaseTaxAmount($total->getBaseTaxAmount());
        $orderTotal->setAmountInvoiced($total->getAmountInvoiced());
        $orderTotal->setBaseAmountInvoiced($total->getBaseAmountInvoiced());
        $orderTotal->setAmountRefunded($total->getAmountRefunded());
        $orderTotal->setBaseAmountRefunded($total->getBaseAmountRefunded());
        $orderTotal->setLabel($total->getLabel());
        $orderTotal->setTypeId($total->getTypeId());
        $orderTotal->setCode($total->getCode());

        $orderTotalGroup->addItem($orderTotal);
        $extensionAttributes->setFoomanTotalGroup($orderTotalGroup);
        $order->setExtensionAttributes($extensionAttributes);
    }
}
