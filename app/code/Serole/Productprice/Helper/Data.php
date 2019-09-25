<?php
/**
 * Created by Serole(Dk) on 05/09/2018.
 * includes the code related to system configuration of Sage Integration.
 */
 
namespace Serole\Productprice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}