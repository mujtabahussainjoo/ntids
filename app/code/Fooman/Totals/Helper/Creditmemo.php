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

class Creditmemo
{
    /**
     * @var \Magento\Sales\Api\Data\CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalFactory
     */
    private $creditmemoTotalFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @param \Magento\Sales\Api\Data\CreditmemoExtensionFactory  $creditmemoExtensionFactory
     * @param \Fooman\Totals\Model\CreditmemoTotalFactory  $creditmemoTotalFactory
     * @param \Fooman\Totals\Model\GroupFactory         $groupFactory
     */
    public function __construct(
        \Magento\Sales\Api\Data\CreditmemoExtensionFactory $creditmemoExtensionFactory,
        \Fooman\Totals\Model\CreditmemoTotalFactory $creditmemoTotalFactory,
        \Fooman\Totals\Model\GroupFactory $groupFactory
    ) {
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
        $this->creditmemoTotalFactory = $creditmemoTotalFactory;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param \Fooman\Totals\Api\Data\CreditmemoTotalInterface $total
     */
    public function setExtensionAttributes(
        \Magento\Sales\Model\Order\Creditmemo $creditmemo,
        \Fooman\Totals\Api\Data\CreditmemoTotalInterface $total
    ) {
        $extensionAttributes = $creditmemo->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->creditmemoExtensionFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\TotalGroupInterface $creditmemoTotalGroup */
        $creditmemoTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$creditmemoTotalGroup) {
            $creditmemoTotalGroup = $this->groupFactory->create();
        }

        /** @var \Fooman\Totals\Api\Data\CreditmemoTotalInterface $creditmemoTotal */
        $creditmemoTotal = $creditmemoTotalGroup->getByTypeId($total->getTypeId());

        if (!$creditmemoTotal) {
            $creditmemoTotal = $this->creditmemoTotalFactory->create();
        }
        
        $creditmemoTotal->setAmount($total->getAmount());
        $creditmemoTotal->setBaseAmount($total->getBaseAmount());
        $creditmemoTotal->setTaxAmount($total->getTaxAmount());
        $creditmemoTotal->setBaseTaxAmount($total->getBaseTaxAmount());
        $creditmemoTotal->setLabel($total->getLabel());
        $creditmemoTotal->setTypeId($total->getTypeId());
        $creditmemoTotal->setCode($total->getCode());

        $creditmemoTotalGroup->addItem($creditmemoTotal);
        $extensionAttributes->setFoomanTotalGroup($creditmemoTotalGroup);
        $creditmemo->setExtensionAttributes($extensionAttributes);
    }
}
