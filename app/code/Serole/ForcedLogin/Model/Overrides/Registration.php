<?php
namespace Serole\ForcedLogin\Model\Overrides;

class Registration extends \Magento\Customer\Model\Registration
{

    private $scopeConfig;


    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }//end __construct()


    public function isAllowed()
    {
        $forced_login_status = $this->scopeConfig->getValue(
            'forcedlogin/parameters/status',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $forced_login_access = $this->scopeConfig->getValue(
            'forcedlogin/parameters/access_to_website',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($forced_login_access == '0' && $forced_login_status == '1') {
            return false;
        }

        return true;
    }//end isAllowed()
}//end class
