<?php

namespace Wizkunde\WebSSO\Helper;

class Eav extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $customerAttributeCollection;
    private $customerAddressAttributeCollection;

    /**
     * Eav constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Attribute\Collection $customerAttributeCollection
     * @param \Magento\Customer\Model\ResourceModel\Address\Attribute\Collection $customerAddressAttributeCollection
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\ResourceModel\Attribute\Collection $customerAttributeCollection,
        \Magento\Customer\Model\ResourceModel\Address\Attribute\Collection $customerAddressAttributeCollection
    ) {

        parent::__construct($context);

        $this->customerAttributeCollection = $customerAttributeCollection;
        $this->customerAddressAttributeCollection = $customerAddressAttributeCollection;
    }

    /**
     * @return array
     */
    public function getCustomerAttributes()
    {
        $attributes = $this->customerAttributeCollection->addVisibleFilter();

        $values = [];
        foreach ($attributes as $attribute) {
            if (($label = $attribute->getFrontendLabel())) {
                $values[] = [
                    'code' => $attribute->getAttributeCode(),
                    'label' => 'Customer: ' . $label
                ];
            }
        }

        $values[] = [
            'code' => 'password',
            'label' => 'Customer: password'
        ];

        return $values;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getCustomerAddressAttributes($type = 'shipping')
    {
        $attributes = $this->customerAddressAttributeCollection->addVisibleFilter();

        $values = [];
        foreach ($attributes as $attribute) {
            if (($label = $attribute->getFrontendLabel())) {
                $values[] = [
                    'code' => $type . '_' . $attribute->getAttributeCode(),
                    'label' => ucfirst($type) . ' Address: ' . $label
                ];
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getAvailableAttributes()
    {
        return array_merge(
            $this->getCustomerAttributes(),
            $this->getCustomerAddressAttributes('shipping'),
            $this->getCustomerAddressAttributes('billing')
        );
    }
}
