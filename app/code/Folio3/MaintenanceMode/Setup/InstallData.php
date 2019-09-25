<?php
namespace Folio3\MaintenanceMode\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    
    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        //--- Add a Default Maintenance Mode Static
        //--- Block Page for the Customer

        $content = '<h2 class="intro">We are sorry but this store is down for maintenance. <br />Please try again later</h2>';

        //--- One Stati Block for All Store Views
        $stores = array(0);

        foreach ($stores as $store) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $block = $objectManager->create('\Magento\Cms\Model\Block');

            $block->setTitle('Site Under Maintenance');
            $block->setIdentifier('f3_maintenance');
            $block->setStores(array($store));
            $block->setIsActive(1);
            $block->setContent($content);

            $block->save();
        }
    }
}