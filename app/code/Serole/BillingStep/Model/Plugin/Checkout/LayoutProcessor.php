<?php
namespace Serole\BillingStep\Model\Plugin\Checkout;
class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
 
        $jsLayout ['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['shippingAddress']['children']['billing-address']['children']['subscribe_container'] = [
            'component' => 'uiComponent',
            'displayArea' => 'subscribe_container'
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
        ['shippingAddress']['children']['billing-address']['children']['subscribe_container']['children']['subscribe_to_newsletter'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'BigBridge_Checkout/form/element/checkbox-overwrite'
            ],
            'provider' => 'checkoutProvider',
            'dataScope' => 'shippingAddress.custom_attributes.subscribe_to_newsletter',
            'description' => __('Subscribe to the newsletter')
        ];
 
 
        return $jsLayout;
    }
}