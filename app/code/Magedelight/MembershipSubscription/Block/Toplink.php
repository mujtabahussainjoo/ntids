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

namespace Magedelight\MembershipSubscription\Block;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Http\Context;

class Toplink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var Url $_customerUrl
     */
    protected $_customerUrl;
    
    /**
     * @var Context $httpContext
     */
    protected $httpContext;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     *
     * @var type
     */
    protected $_template = 'Magedelight_MembershipSubscription::link.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @param Url $customerUrl
     * @param Context $httpContext
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Url $customerUrl,
        Context $httpContext,
        array $data = []
    ) {
    
        $this->_customerUrl = $customerUrl;
        $this->httpContext = $httpContext;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function getHref()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $url = trim($this->scopeConfig->getValue('membership/membership_settings/identifier', $storeScope));

        return $this->getUrl($url);
    }
    
    /**
     *
     * @return label
     */
    public function getLabel()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $label = $this->scopeConfig->getValue('membership/membership_settings/title', $storeScope);

        return __($label);
    }
}
