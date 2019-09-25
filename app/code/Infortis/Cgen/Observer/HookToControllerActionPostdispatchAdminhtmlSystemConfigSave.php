<?php

namespace Infortis\Cgen\Observer;

use Infortis\Cgen\Observer\AbstractObserver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class HookToControllerActionPostdispatchAdminhtmlSystemConfigSave extends AbstractObserver implements ObserverInterface
{    
    /**
     * @var RequestInterface
     */
    protected $appRequestInterface;

    /**
     * @var \Infortis\Cgen\Helper\Definitions
     */
    protected $configHelper;

    public function __construct(
        RequestInterface $appRequestInterface,
        \Infortis\Cgen\Helper\Definitions $configHelper,
        \Infortis\Cgen\Helper\AssetCache $assetCacheHelper
    ) {
        $this->appRequestInterface = $appRequestInterface;
        $this->configHelper = $configHelper;

        parent::__construct($assetCacheHelper);
    }

    /**
     * After any system config is saved
     */
    public function hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave()
    {
        $sectionId = $this->appRequestInterface->getParam('section');
        if ($sectionId == 'theme_layout')
        {
            $tags = $this->configHelper->getSectionTags($sectionId);
            $this->cleanDynamicCacheByTag($tags);
        }
        elseif ($sectionId == 'theme_design')
        {
            $tags = $this->configHelper->getSectionTags($sectionId);
            $this->cleanDynamicCacheByTag($tags);
        }
        elseif ($sectionId == 'theme_settings')
        {
            $tags = $this->configHelper->getSectionTags($sectionId);
            $this->cleanDynamicCacheByTag($tags);
        }
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return $this->hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave();
    }   
}
