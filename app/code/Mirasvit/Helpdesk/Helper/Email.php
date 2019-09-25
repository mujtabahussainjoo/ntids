<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

class Email
{
    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Helpdesk\Helper\StringUtil
     */
    protected $helpdeskString;
    /**
     * @var \Mirasvit\Helpdesk\Helper\Process
     */
    protected $helpdeskProcess;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    private $string;

    /**
     * @param \Mirasvit\Helpdesk\Model\Config $config
     * @param \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory
     * @param StringUtil $helpdeskString
     * @param Process $helpdeskProcess
     * @param Encoding $helpdeskEncoding
     * @param Customer $helpdeskCustomer
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory,
        \Mirasvit\Helpdesk\Helper\StringUtil $helpdeskString,
        \Mirasvit\Helpdesk\Helper\Process $helpdeskProcess,
        \Mirasvit\Helpdesk\Helper\Encoding $helpdeskEncoding,
        \Mirasvit\Helpdesk\Helper\Customer $helpdeskCustomer,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->config                = $config;
        $this->gatewayFactory        = $gatewayFactory;
        $this->helpdeskString        = $helpdeskString;
        $this->helpdeskProcess       = $helpdeskProcess;
        $this->helpdeskEncoding      = $helpdeskEncoding;
        $this->helpdeskCustomer      = $helpdeskCustomer;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->string                = $string;
        $this->context               = $context;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     *
     * @return bool|\Mirasvit\Helpdesk\Model\Ticket
     */
    public function processEmail($email)
    {
        $code = $this->helpdeskString->getTicketCodeFromSubject($email->getSubject());
        if (!$code) {
            $code = $this->helpdeskString->getTicketCodeFromBody($email->getBody());
        }

        if (strpos($code, 'RMA') === 0 && $this->getConfig()->isActiveRma()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Mirasvit\Rma\Helper\Process $rmaProcess */
            $rmaProcess = $objectManager->create('Mirasvit\Rma\Helper\Process');

            return $rmaProcess->processEmail($email, $code);
        } else {
            return $this->helpdeskProcess->processEmail($email, $code);
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param string                          $subject
     *
     * @return string
     */
    public function getEmailSubject($ticket, $subject = '')
    {
        $result = '';
        if ($this->config->getNotificationIsShowCode()) {
            $result = "[#{$ticket->getCode()}] ";
        }
        if ($subject) {
            $result .= "$subject - ";
        }
        $result .= $ticket->getSubject();

        return $result;
    }

    /**
     * @param string $code
     *
     * @return string
     */
    public function getHiddenCode($code)
    {
        return
            "<span style='color:#FFFFFF;font-size:5px;margin:0px;padding:0px;display:block;height:0;'>".
                "Message-Id:--#{$code}--".
            "</span>";
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return '##- ' . __('please type your reply above this line') . ' -##';
    }

    /**
     * @return string
     */
    public function getHiddenSeparator()
    {
        return "<span style='color:#FFFFFF;font-size:5px;margin:0px;padding:0px;display:block;height:0;'>" . $this->getSeparator() . '</span>';
    }

    /**
     * @param string $preheaderText
     * @return string
     */
    public function getPreheaderText($preheaderText)
    {
        $maxLen = 150;
        $len = $this->string->strlen($preheaderText);
        if ($len < $maxLen) {
            $preheaderText = str_pad($preheaderText, $maxLen, ' ');
        } else {
            $preheaderText = $this->string->substr($preheaderText, 0, $maxLen);
        }
        return "<span style='opacity:0;color:transparent;font-size:5px;margin:0px;padding:0px;display:block;height:0;'>" .
                $preheaderText. '  &nbsp;&nbsp;&nbsp;</span>';
    }

    /**
     * Removes emails of gateways from the list
     *
     * @param array $emails
     *
     * @return array
     */
    public function stripGatewayEmails($emails)
    {
        $collection = $this->gatewayFactory->create()->getCollection()->addFieldToFilter('is_active', 1);

        if ($collection->count() && $emails) {
            foreach ($collection as $gateway) {
                foreach ($emails as $k => $email) {
                    if ($gateway->getEmail() == $email) {
                        unset($emails[$k]);
                    }
                }
            }
        }

        return $emails;
    }
}
