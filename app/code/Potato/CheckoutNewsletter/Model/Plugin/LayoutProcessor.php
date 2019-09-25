<?php
namespace Potato\CheckoutNewsletter\Model\Plugin;

use Potato\CheckoutNewsletter\Model\Config;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor
{
    /** @var Config  */
    protected $config;

    /**
     * LayoutProcessor constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        if (!$this->config->isEnabled() || !$this->config->isDisplayCheckbox() || !$this->config->canSubscribe()) {
            return $jsLayout;
        }

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
        ['children']['afterMethods']['children']['po_checkout_newsletter'] = [
            'component' => 'Magento_Ui/js/form/element/single-checkbox',
            'config' => [
                'checked' => (bool)$this->config->isChecked(),
                'description' => $this->config->getStorefrontLabel(),
                'visible' => true,
                'template' => 'ui/form/field',
                'elementTmpl' => 'Potato_CheckoutNewsletter/form/components/checkbox',
            ],
            'provider' => 'checkoutProvider',
            'validation' => [],
        ];
        return $jsLayout;
    }
}