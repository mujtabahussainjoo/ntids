<?php
namespace Potato\CheckoutNewsletter\Model\Plugin;

use Magento\Checkout\Api\AgreementsValidatorInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\Message\ManagerInterface;
use Potato\CheckoutNewsletter\Model\Config;
use Magento\Customer\Model\Session as CustomerSession;


/**
 * Class ProcessSubscribe
 */
class ProcessSubscribe
{
    /** @var ScopeConfigInterface  */
    protected $scopeConfiguration;

    /** @var CheckoutAgreementsRepositoryInterface  */
    protected $checkoutAgreementsRepository;

    /** @var AgreementsValidatorInterface  */
    protected $agreementsValidator;

    /** @var Config  */
    protected $config;

    /** @var Subscriber  */
    protected $subscriber;

    /** @var ManagerInterface  */
    protected $messageManager;
    
    /** @var CustomerSession  */
    protected $customerSession;

    /**
     * ProcessSubscribe constructor.
     * @param AgreementsValidatorInterface $agreementsValidator
     * @param ScopeConfigInterface $scopeConfiguration
     * @param CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository
     * @param Config $config
     * @param Subscriber $subscriber
     * @param ManagerInterface $messageManager
     * @param CustomerSession $customerSession
     */
    public function __construct(
        AgreementsValidatorInterface $agreementsValidator,
        ScopeConfigInterface $scopeConfiguration,
        CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository,
        Config $config,
        Subscriber $subscriber,
        ManagerInterface $messageManager,
        CustomerSession $customerSession
    ) {
        $this->agreementsValidator = $agreementsValidator;
        $this->scopeConfiguration = $scopeConfiguration;
        $this->checkoutAgreementsRepository = $checkoutAgreementsRepository;
        $this->config = $config;
        $this->subscriber = $subscriber;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
    }

    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $extensionAttributes = $paymentMethod->getExtensionAttributes();
        if (!$extensionAttributes) {
            return;
        }
        $isSubscribed = (bool)$extensionAttributes->getPoNewsletterSubscribe();
        if (!$this->config->isEnabled() || !$this->config->canSubscribe()
            || ($this->config->isDisplayCheckbox() && !$isSubscribed)
        ) {
            return;
        }
        $email = $this->customerSession->getCustomer()->getEmail();
        $status = $this->subscriber->subscribe($email);
        if ($status == Subscriber::STATUS_NOT_ACTIVE) {
            $this->messageManager->addSuccessMessage(__('Confirmation request has been sent.'));
        } else {
            $this->messageManager->addSuccessMessage(__('Thank you for your subscription.'));
        }
    }
}
