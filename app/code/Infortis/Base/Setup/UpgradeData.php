<?php

namespace Infortis\Base\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    protected $resourceConfig;

    public function __construct(
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $resourceConfig
    ) {
        $this->resourceConfig = $resourceConfig;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.5.0') < 0)
        {
            $this->resourceConfig->saveConfig(
                'cms/wysiwyg/enabled', 
                \Magento\Cms\Model\Wysiwyg\Config::WYSIWYG_HIDDEN, // 'hidden' (Disabled by Default)
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, // 'default'
                \Magento\Store\Model\Store::DEFAULT_STORE_ID // 0
            );

            $this->resourceConfig->saveConfig(
                'checkout/sidebar/display', // Display Shopping Cart Sidebar
                true,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, // 'default'
                \Magento\Store\Model\Store::DEFAULT_STORE_ID // 0
            );
        }

        $setup->endSetup();
    }
}
