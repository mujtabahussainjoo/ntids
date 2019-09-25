<?php
namespace Potato\CheckoutNewsletter\Model\Plugin;

use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\Message\ManagerInterface;
use Potato\CheckoutNewsletter\Model\Config;


/**
 * Class GuestProcessSubscribe
 */
class GuestProcessSubscribe
{
    /** @var Config  */
    protected $config;

    /** @var Subscriber  */
    protected $subscriber;

    /** @var ManagerInterface  */
    protected $messageManager;

    /**
     * GuestProcessSubscribe constructor.
     * @param Config $config
     * @param Subscriber $subscriber
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Config $config,
        Subscriber $subscriber,
        ManagerInterface $messageManager
    ) {
        $this->config = $config;
        $this->subscriber = $subscriber;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
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
        $status = $this->subscriber->loadByEmail($email)->getStatus();
        if (!$this->config->isDisplayCheckbox() && $this->config->isNotSubscribeForUnsubscribed() && $status == Subscriber::STATUS_UNSUBSCRIBED) {
            return;
        }
        $status = $this->subscriber->subscribe($email);
        if ($status == Subscriber::STATUS_NOT_ACTIVE) {
            $this->messageManager->addSuccessMessage(__('Confirmation request has been sent.'));
        } else {
            $this->messageManager->addSuccessMessage(__('Thank you for your subscription.'));
        }
    }
}
