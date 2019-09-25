<?php

    namespace Serole\Corefiles\Model\Checkout\Plugin;

    class Validation extends \Magento\CheckoutAgreements\Model\Checkout\Plugin\Validation{

        /*public function __construct(\Magento\Checkout\Api\AgreementsValidatorInterface $agreementsValidator,
                                    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfiguration,
                                    \Magento\CheckoutAgreements\Api\CheckoutAgreementsListInterface $checkoutAgreementsList,
                                     ActiveStoreAgreementsFilter $activeStoreAgreementsFilter)
        {
            parent::__construct($agreementsValidator, $scopeConfiguration, $checkoutAgreementsList, $activeStoreAgreementsFilter);
        }*/

        public function beforeSavePaymentInformation(
            \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
            $cartId,
            \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
            \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
        ) {
            if ($this->isAgreementEnabled()) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/checkout-aggrements.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info("Hiiiii");
                //$logger->info($documentList);
                //$this->validateAgreements($paymentMethod);
            }
        }
    }

