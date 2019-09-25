<?php
namespace Folio3\MaintenanceMode\Observers;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class EnableMaintenanceMode implements ObserverInterface
{
    /**
     * Execute MaintenanceMode if Enabled
     *
     * @return string
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getResponse();
        $helper = ObjectManager::getInstance()->get('\Folio3\MaintenanceMode\Helper\Data');
        if ($helper->isMaintenanceMode()) {
            $maintenanceBlock = $helper->getMaintenancePageBlock();
            $response->setBody($maintenanceBlock->toHtml());
        }
    }
}