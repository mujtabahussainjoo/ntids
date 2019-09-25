<?php
namespace Potato\CheckoutNewsletter\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Module\Manager;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Checkout\Model\Session as CheckoutSession;

/**
 * Class Config
 */
class Config
{
    const GENERAL_IS_ENABLED = 'potato_checkout_newsletter/general/is_enabled';
    const GENERAL_IS_DISPLAY = 'potato_checkout_newsletter/general/is_display_checkbox';
    const GENERAL_IS_CHECKED = 'potato_checkout_newsletter/general/is_checked';
    const GENERAL_IS_NOT_CHECKED_UNSUBSCRIBED = 'potato_checkout_newsletter/general/is_not_checked_for_unsubscribed';
    const GENERAL_IS_NOT_SUBSCRIBE_UNSUBSCRIBED = 'potato_checkout_newsletter/general/is_not_subscribed_for_unsubscribed';
    const GENERAL_STOREFRONT_LABEL = 'potato_checkout_newsletter/general/storefront_label';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var Manager  */
    protected $moduleManager;

    /** @var Subscriber  */
    protected $subscriber;

    /** @var RequestInterface  */
    protected $request;

    /** @var CustomerSession  */
    protected $customerSession;
    
    /** @var  CheckoutSession */
    protected $checkoutSession;

    /** @var  SubscriberFactory */
    protected $subscriberFactory;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Manager $moduleManager
     * @param Subscriber $subscriber
     * @param RequestInterface $request
     * @param CustomerSession $customerSession
     * @param SubscriberFactory $subscriberFactory
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        Subscriber $subscriber,
        RequestInterface $request,
        CustomerSession $customerSession,
        SubscriberFactory $subscriberFactory,
        CheckoutSession $checkoutSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->moduleManager = $moduleManager;
        $this->subscriber = $subscriber;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
        $this->checkoutSession = $checkoutSession;
    }
    
    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isCheckedByDefault($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_IS_CHECKED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isNotCheckedForUnsubscribed($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_IS_NOT_CHECKED_UNSUBSCRIBED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isNotSubscribeForUnsubscribed($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_IS_NOT_SUBSCRIBE_UNSUBSCRIBED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayCheckbox($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_IS_DISPLAY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getStorefrontLabel($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $this->scopeConfig->getValue(
            self::GENERAL_STOREFRONT_LABEL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $isEnabled =  $this->scopeConfig->getValue(
            self::GENERAL_IS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $isModuleEnabled = $this->moduleManager->isEnabled('Potato_CheckoutNewsletter');
        $isModuleOutputEnabled = $this->moduleManager->isOutputEnabled('Potato_CheckoutNewsletter');
        return $isEnabled && $isModuleEnabled && $isModuleOutputEnabled;
    }

    /**
     * @return bool
     */
    public function canSubscribe()
    {
        if (!$this->canSubscribeByGuest()) {
            return false;
        }
        if ($this->isAlreadySubscribed()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function canSubscribeByGuest()
    {
        $allowGuestSubscribe = $this->scopeConfig->isSetFlag(
            Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG
        );
        if (
            $allowGuestSubscribe == false && !$this->customerSession->isLoggedIn()
        ) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isAlreadySubscribed()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        $customerModel = $this->customerSession->getCustomer();
        $customerEmail = $customerModel->getEmail();

        $subscriber = $this->subscriber->loadByEmail($customerEmail);
        if (!$subscriber->getId()){
            return false;
        }

        if ($subscriber->getStatus() == Subscriber::STATUS_UNSUBSCRIBED) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isCheckoutPage()
    {
        $checkoutControllerModule = 'Magento_Checkout';
        $controllerModule = $this->request->getControllerModule();
        return $controllerModule == $checkoutControllerModule;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        if ($this->customerSession->isLoggedIn() === true) {
            $email = $this->customerSession->getCustomer()->getEmail();
        } else {
            $email = $this->checkoutSession->getQuote()->getCustomerEmail();
        }
        $subscriber = $this->subscriber->loadByEmail($email);
        return $this->isCheckedByDefault() && !($this->isNotCheckedForUnsubscribed() && $subscriber->getSubscriberStatus() == Subscriber::STATUS_UNSUBSCRIBED);
    }
}