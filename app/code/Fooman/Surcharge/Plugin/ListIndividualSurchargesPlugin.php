<?php

namespace Fooman\Surcharge\Plugin;

class ListIndividualSurchargesPlugin
{

    /**
     * @var \Magento\Quote\Api\Data\TotalSegmentExtensionFactory
     */
    private $totalSegmentExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\QuoteAddressGroupFactory
     */
    private $groupFactory;

    public function __construct(
        \Magento\Quote\Api\Data\TotalSegmentExtensionFactory $totalSegmentExtensionFactory,
        \Fooman\Totals\Model\QuoteAddressGroupFactory $groupFactory
    ) {
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->groupFactory = $groupFactory;
    }

    /**
     * @param \Magento\Quote\Model\Cart\TotalsConverter $subject
     * @param \Closure                                  $proceed
     * @param array                                     $addressTotals
     *
     * @return mixed
     */
    public function aroundProcess(
        \Magento\Quote\Model\Cart\TotalsConverter $subject,
        \Closure $proceed,
        array $addressTotals = []
    ) {

        $totalSegments = $proceed($addressTotals);
        if (!isset($totalSegments['fooman_surcharge'])) {
            return $totalSegments;
        }

        $surchargeSegment = $addressTotals['fooman_surcharge'];
        $extensionAttributes = $surchargeSegment->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->totalSegmentExtensionFactory->create();
        }

        $group = $extensionAttributes->getFoomanSurchargeDetails();
        if ($group === null) {
            $group = $this->groupFactory->create();
        }

        $surcharges = $surchargeSegment->getFullInfo();
        if (!empty($surcharges)) {
            foreach ($surcharges as $item) {
                $group->addItem($item);
            }
        }

        $extensionAttributes->setFoomanSurchargeDetails($group);
        $totalSegments['fooman_surcharge']->setExtensionAttributes($extensionAttributes);

        return $totalSegments;
    }
}
