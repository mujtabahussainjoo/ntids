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

namespace Magedelight\MembershipSubscription\Observer;

use Magento\Framework\Event\ObserverInterface;

class Mdfrd implements ObserverInterface
{
    /**
     * Core store config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Url\ScopeResolverInterface
     */
    protected $_context;

    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magedelight\MembershipSubscription\Helper\Mddata $helper,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->messageManager = $context->getMessageManager();
        $this->_urlBuilder = $context->getUrl();
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent()->getName();
        if ($_SERVER['SERVER_NAME'] != 'localhost' && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
            $keys['serial_key'] = $this->_scopeConfig->getValue('membership/license/serial_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $keys['activation_key'] = $this->_scopeConfig->getValue('membership/license/activation_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            if(!empty($keys['serial_key']) && !empty($keys['activation_key']))
            {
                $url = $this->_urlBuilder->getCurrentUrl();
                $parsedUrl = parse_url($url);
                $keys['host'] = $parsedUrl['host'];
                $keys['ip'] = $_SERVER['SERVER_ADDR'];
                $keys['product_name'] = 'Membership Subscription';
                $keys['new_mechanism'] = 1;
                $keys['extension_key'] = $this->helper->getExtensionKey();
                $field_string = http_build_query($keys);
                $ch = curl_init('https://www.magedelight.com/ktplsys/?'.$field_string);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                try {
                    curl_exec($ch);
                    curl_close($ch);
                } catch (\Exception $e) {
                    //$this->messageManager->addError($e->getMessage());
                }
            }
        }
    }
}
