<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Model\Total\Quote\Surcharge;

class BeforeTaxCalc extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    const CODE = 'fooman_surcharge_tax';

    /**
     * @var \Fooman\Surcharge\Model\SurchargeTaxCalculation
     */
    private $surchargeTaxCalculation;

    /**
     * @param \Fooman\Surcharge\Model\SurchargeTaxCalculation  $surchargeTaxCalculation
     */
    public function __construct(
        \Fooman\Surcharge\Model\SurchargeTaxCalculation $surchargeTaxCalculation
    ) {
        $this->surchargeTaxCalculation = $surchargeTaxCalculation;
    }

    /**
     * @param \Magento\Quote\Model\Quote                          $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     *
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();

        if (empty($address->getAllItems())) {
            return $this;
        }

        $extensionAttributes = $address->getExtensionAttributes();
        if ($extensionAttributes === null || is_array($extensionAttributes)) {
            return $this;
        }

        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            return $this;
        }

        $foomanSurchargeTotalsItems = $foomanTotalGroup->getItems();
        if (empty($foomanSurchargeTotalsItems)) {
            return $this;
        }

        foreach ($foomanSurchargeTotalsItems as $foomanSurchargeTotalsItem) {
             $this->surchargeTaxCalculation->calculateTotalsTaxAmount($foomanSurchargeTotalsItem, $quote, $address);
        }

        // Surcharge Tax is already added to Grand total via standard Tax Total
        // so it's included in the full tax summary
        // => no $total->setTotalAmount() needed here

        return $this;
    }

    public function getLabel()
    {
        return __('Surcharge Tax');
    }

    /**
     * @param \Magento\Quote\Model\Quote               $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return null;
    }
}
