<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model;

class Mailsender
{
    const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE = 'newsletter/subscription/confirm_email_identity';

    protected $_scopeConfig;
    protected $_storeManager;
    protected $_objectManager;
    protected $_transportBuilder;
    protected $inlineTranslation;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger = $logger;
    }

    public function sendMail($data, $type, $mail)
    {
        switch ($type) {
            case 'success':
                $template = $this->_scopeConfig->getValue('amaudit/successful_log_mailing/template');
                break;
            case 'unsuccessful':
                $template = $this->_scopeConfig->getValue('amaudit/unsuccessful_log_mailing/template');
                break;
            case 'suspicious':
                $template = $this->_scopeConfig->getValue('amaudit/suspicious_log_mailing/template');
                break;
        }
        //template use recipient name without 'fullname'
        $data['fullname'] = isset($data['name']) ? $data['name'] : NULL;

        $parseDataVars = new \Magento\Framework\DataObject();

        $parseDataVars->setData($data);

        if (isset($template)) {
            $sender = array(
                'name' => $this->_scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'email' => $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            );
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => $this->getStoreId()])
                ->setTemplateVars(array('data' => $parseDataVars))
                ->setFrom($sender)
                ->addTo($mail, $mail)
                ->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Exception $exception) {
                $this->logger->warning($exception);
            }
        }
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
