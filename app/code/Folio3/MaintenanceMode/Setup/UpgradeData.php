<?php

namespace Folio3\MaintenanceMode\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface {

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $content = '<h2 class="intro">We are sorry but this store is down for maintenance. <br />Please try again later</h2>';
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $block = $objectManager->create('\Magento\Cms\Model\Block');
            $block->load('f3_maintenance', 'identifier');
            $block->setContent($content);
            $block->save();
        }
    }

}
