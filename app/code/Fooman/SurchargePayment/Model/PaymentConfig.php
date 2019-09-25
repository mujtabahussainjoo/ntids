<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_SurchargePayment
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\SurchargePayment\Model;

use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\Config as CorePaymentConfig;

class PaymentConfig
{

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * @var CorePaymentConfig
     */
    private $paymentConfig;

    public function __construct(
        ManagerInterface $eventManager,
        PaymentHelper $paymentHelper,
        CorePaymentConfig $paymentConfig
    ) {
        $this->eventManager = $eventManager;
        $this->paymentHelper = $paymentHelper;
        $this->paymentConfig = $paymentConfig;
    }

    public function getGroupedList()
    {
        $result = [];
        $groups = $this->paymentConfig->getGroups();
        $methods = $this->paymentHelper->getPaymentMethods();
        asort($methods);

        foreach ($groups as $code => $title) {
            $result[$code] = ['label' => $title];
        }

        foreach ($methods as $code => $methodData) {
            try {
                $label = $this->paymentHelper->getMethodInstance($code)->getConfigData('title');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $label = false;
            }

            if (empty($label)) {
                $label = (isset($methodData['title']) && !empty($methodData['title'])) ? $methodData['title'] : $code;
            }

            if (isset($methodData['group']) && isset($result[$methodData['group']])) {
                $result[$methodData['group']]['value'][$code] = ['value' => $code, 'label' => $label];
            } else {
                $result[$code] = ['value' => $code, 'label' => $label];
            }
        }

        //Add the payment method code to be able differentiate all the Paypal methods
        if (isset($result['paypal'])) {
            foreach ($result['paypal']['value'] as $paymentMethodCode => $paymentMethod) {
                $result['paypal']['value'][$paymentMethodCode]['label'] =
                    $paymentMethod['label'] . " ($paymentMethodCode)";
            }
        }

        $transport = new \Magento\Framework\DataObject(
            ['payment_methods' => $result]
        );

        $this->eventManager->dispatch('fooman_surcharge_payment_method_select', ['transport' => $transport]);

        return $transport->getPaymentMethods();
    }
}
