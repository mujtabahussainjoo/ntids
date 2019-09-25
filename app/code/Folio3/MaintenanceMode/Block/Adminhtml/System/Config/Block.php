<?php
namespace Folio3\MaintenanceMode\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;

class Block extends Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!is_numeric($element->getValue())) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $maintenanceBlock = $objectManager->create('\Magento\Cms\Model\Block')->load($element->getValue(), 'identifier');
            //$maintenanceBlock = Mage::getModel('cms/block')->load($element->getValue(), 'identifier');

            if ($maintenanceBlock->getBlockId()) {
                $element->setValue($maintenanceBlock->getBlockId());
            }
        }

        return $element->getElementHtml();
    }
}