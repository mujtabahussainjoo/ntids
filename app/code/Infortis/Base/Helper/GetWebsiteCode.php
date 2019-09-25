<?php

namespace Infortis\Base\Helper;

class GetWebsiteCode
{
    protected $storeManager;
    
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;

        //TODO:
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/infortis.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Deprecated helper Infortis\Base\Helper\GetWebsiteCode::__construct()');
    }
    
    public function getCodeByScope($scope_id)
    {
        return $this->storeManager->getWebsite($scope_id)->getCode();
    }
}
