<?php

namespace Fooman\Surcharge\Plugin;

class AppliedTaxes
{

    private $taxManagement;

    public function __construct(
        \Magento\Tax\Api\OrderTaxManagementInterface $taxManagement
    ) {
        $this->taxManagement = $taxManagement;
    }

    public function aroundGetCalculatedTaxes(
        \Magento\Tax\Helper\Data $subject,
        \Closure $proceed,
        $source
    ) {
        $result = $proceed($source);
        if ($source instanceof \Magento\Sales\Api\Data\InvoiceInterface
            || $source instanceof \Magento\Sales\Api\Data\CreditmemoInterface
        ) {
            $taxDetails = $this->taxManagement->getOrderTaxDetails($source->getOrderId());
            foreach ($taxDetails->getItems() as $taxDetail) {
                if ($taxDetail->getType() == 'fooman_surcharge') {
                    $result = $this->aggregateTax($result, $taxDetail->getAppliedTaxes());
                }
            }
        }

        return $result;
    }

    private function aggregateTax($result, $appliedTaxes)
    {

        foreach ($appliedTaxes as $appliedTax) {
            $found = false;
            if (is_array($result)) {
                foreach ($result as $key => $taxRate) {
                    if ($taxRate['title'] == $appliedTax->getTitle()
                        && $taxRate['percent'] == $appliedTax->getPercent()
                    ) {
                        $found = true;
                        $result[$key]['tax_amount'] += $appliedTax->getAmount();
                        $result[$key]['base_tax_amount'] += $appliedTax->getBaseAmount();
                    }
                }
            }

            if (!$found) {
                if (!is_array($result)) {
                    $result = [];
                }
                $result[$appliedTax->getCode()] = [
                    'tax_amount' => $appliedTax->getAmount(),
                    'base_tax_amount' => $appliedTax->getBaseAmount(),
                    'title' => $appliedTax->getTitle(),
                    'percent' => $appliedTax->getPercent()
                ];
            }
        }
        return $result;
    }
}
