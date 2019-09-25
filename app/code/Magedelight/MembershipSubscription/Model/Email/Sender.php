<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */


namespace Magedelight\MembershipSubscription\Model\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Framework\Json\Helper\Data;

class Sender
{

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_subscription;

    /**
     * @var subscriptionEmailSender
     */
    protected $_subscriptionEmailSender;

    /**
     * @var type
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var subscriptionProfile
     */
    protected $_subscriptionProfile;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     *
     * @param Renderer $addressRenderer
     * @param TransportBuilder $transportBuilder
     * @param PaymentHelper $paymentHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Store\Model\StoreFactory $_storeFactory
     * @param \Magento\Catalog\Model\ProductFactory $_product
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param Data $jsonHelper
     */
    public function __construct(
        Renderer $addressRenderer,
        TransportBuilder $transportBuilder,
        PaymentHelper $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Store\Model\StoreFactory $_storeFactory,
        \Magento\Catalog\Model\ProductFactory $_product,
        \Magento\User\Model\UserFactory $userFactory,
        Data $jsonHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->addressRenderer = $addressRenderer;
        $this->paymentHelper = $paymentHelper;
        $this->_localeCurrency = $localeCurrency;
        $this->_userFactory = $userFactory;
        $this->_storeFactory = $_storeFactory;
        $this->_product = $_product;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     *
     * @param type $emailSender
     * @param type $transport
     * @param type $templateId
     * @param type $templateOptions
     * @param type $customerName
     * @param type $customerEmail
     * @param type $copyTo
     * @return type
     */
    protected function sendEmail($emailSender, $transport, $templateId, $templateOptions, $customerName, $customerEmail, $copyTo)
    {
        $this->transportBuilder->setTemplateIdentifier($templateId);
        $this->transportBuilder->setTemplateOptions($templateOptions);
        $this->transportBuilder->setTemplateVars($transport, $customerName);
        $this->transportBuilder->setFrom($emailSender);
        $this->transportBuilder->addTo(
            $customerEmail,
            $customerName
        );
        if (!empty($copyTo)) {
            $copyTo = explode(',', $copyTo);
            foreach ($copyTo as $email) {
                $this->transportBuilder->addBcc($email);
            }
        }
        $transportBuilder = $this->transportBuilder->getTransport();
        $transportBuilder->sendMessage();

        return;
    }

    /**
     * @return array
     */
    protected function getTemplateOptions()
    {
        return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        ];
    }

    /**
     * @param type $order
     * @param type $paymentType
     */
    public function sendReminderEmail($expiryInfo, $customerEmail)
    {

        $emailSender = $this->_scopeConfig->getValue('sales_email/order/identity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $templateId = "magedelight_membershipSubscription_membership_expiry_inform_mail_template";

        $transport = [
            'expiry' => $expiryInfo,
        ];

        $templateOptions = $this->getTemplateOptions();
        $copyTo = "";
        $customerName = "";

        $this->sendEmail($emailSender, $transport, $templateId, $templateOptions, $customerName, $customerEmail, $copyTo);
    }
}
