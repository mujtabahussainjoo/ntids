<?php

namespace Infortis\Ultimo\Helper;

class GetWebsiteCode
{
    protected $storeManager;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }
    
    public function getCodeByScope($scope_id)
    {
        return $this->storeManager->getWebsite($scope_id)->getCode();
    }
}
