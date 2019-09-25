<?php

namespace Infortis\Cgen\Observer;

use Infortis\Cgen\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class HookToDataporterCfgporterImportAfter extends AbstractObserver implements ObserverInterface
{
    public function __construct(
        \Infortis\Cgen\Helper\AssetCache $assetCacheHelper
    ) {
        parent::__construct($assetCacheHelper);
    }

    /**
     * After config import
     */
    public function execute(Observer $observer)
    {
        $this->cleanDynamicCssCache();
    }
}
