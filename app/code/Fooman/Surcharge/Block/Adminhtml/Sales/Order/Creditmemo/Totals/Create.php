<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Block\Adminhtml\Sales\Order\Creditmemo\Totals;

class Create extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Totals
{
    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalManagement
     */
    private $creditmemoTotalManagement;

    /**
     * @var \Fooman\Totals\Model\OrderTotalManagement
     */
    private $orderTotalManagement;

    /**
     * @var \Fooman\Surcharge\Helper\Surcharge
     */
    private $surchargeHelper;

    /**
     * @var \Fooman\Surcharge\Helper\Currency
     */
    private $currencyHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Magento\Sales\Helper\Admin                       $adminHelper
     * @param \Fooman\Totals\Model\CreditmemoTotalManagement    $creditmemoTotalManagement
     * @param \Fooman\Totals\Model\OrderTotalManagement         $orderTotalManagement
     * @param \Fooman\Surcharge\Helper\Surcharge                $surchargeHelper
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Fooman\Totals\Model\CreditmemoTotalManagement $creditmemoTotalManagement,
        \Fooman\Totals\Model\OrderTotalManagement $orderTotalManagement,
        \Fooman\Surcharge\Helper\Surcharge $surchargeHelper,
        \Fooman\Surcharge\Helper\Currency $currencyHelper,
        array $data = []
    ) {
        $this->creditmemoTotalManagement = $creditmemoTotalManagement;
        $this->orderTotalManagement = $orderTotalManagement;
        $this->surchargeHelper = $surchargeHelper;
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $extensionAttributes = $this->getCreditmemo()->getExtensionAttributes();

        if ($extensionAttributes &&
            is_object($creditmemoTotalGroup = $extensionAttributes->getFoomanTotalGroup())
        ) {
            $creditmemoTotals = $creditmemoTotalGroup->getItems();
        } else {
            $creditmemoTotals = $this->creditmemoTotalManagement->getByCodeAndCreditmemoId(
                \Fooman\Surcharge\Model\Surcharge::CODE,
                $this->getCreditmemo()->getId()
            );
        }

        if (empty($creditmemoTotals)) {
            return $this;
        }

        $parent = $this->getParentBlock();

        $addTotal = false;
        $storeId = $this->getCreditmemo()->getStoreId();
        $taxExcl = $this->surchargeHelper->isSalesDisplayedTaxExclusive($storeId);

        $addAfterTotal = 'tax';
        if ($this->_scopeConfig->isSetFlag(\Magento\Tax\Model\Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL)) {
            $addAfterTotal = 'shipping';
        }

        foreach ($creditmemoTotals as $creditmemoTotal) {
            $addTotal = true;
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $creditmemoTotal->getCode(),
                    'label' => __($creditmemoTotal->getLabel()),
                    'block_name' => 'fooman_order_creditmemo_surcharge_totals'
                ]
            );

            if ($taxExcl) {
                $total->setLabel($total->getLabel() . ' ' . __('Excl. Tax'));
            } else {
                $total->setLabel($total->getLabel() . ' ' . __('Incl. Tax'));
            }

            $orderTotal = $this->getOrderTotal($creditmemoTotal);

            $info[$creditmemoTotal->getTypeId()]['label'] = __($total->getLabel());
            $info[$creditmemoTotal->getTypeId()]['type_id'] = $creditmemoTotal->getTypeId();
            $info[$creditmemoTotal->getTypeId()]['value'] = $creditmemoTotal->getAmount();

            if (!$taxExcl) {
                $factor = $info[$creditmemoTotal->getTypeId()]['value'] / $orderTotal->getAmount();
                $info[$creditmemoTotal->getTypeId()]['value'] += $this->currencyHelper->round(
                    $orderTotal->getTaxAmount() * $factor
                );
            }
        }

        if ($addTotal) {
            $parent->addTotal($total, $addAfterTotal);
            $this->setFoomanTotalsInfo($info);
        }

        return $this;
    }

    /**
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface
     */
    protected function getOrderTotal($creditmemoTotal)
    {
        $orderTotals = $this->orderTotalManagement->getByTypeIdAndOrderId(
            $creditmemoTotal->getTypeId(),
            $this->getCreditmemo()->getOrder()->getId()
        );

        return array_shift($orderTotals);
    }

    /**
     * @return \Fooman\Totals\Api\Data\OrderTotalInterface
     */
    protected function getAllCreditmemoTotals()
    {
        $creditmemoTotals = $this->creditmemoTotalManagement->getByOrderId(
            $this->getCreditmemo()->getOrder()->getId()
        );

        return $creditmemoTotals;
    }
}
