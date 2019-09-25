<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get config data frontend enabled
     *
     * @return mixed
     */
    public function checkFrontendEnabled()
    {
        return (bool)$this->getConfig('wizkunde/websso/enabled_frontend');
    }

    /**
     * Get config data backend enabled
     *
     * @return mixed
     */
    public function checkBackendEnabled()
    {
        return (bool)$this->getConfig('wizkunde/websso/enabled_backend');
    }
}
