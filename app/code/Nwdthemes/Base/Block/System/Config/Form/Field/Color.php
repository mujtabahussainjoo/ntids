<?php

namespace Nwdthemes\Base\Block\System\Config\Form\Field;

/**
 * Class Color
 * @package Nwdthemes\Base\Block\System\Config\Form\Field
 *
 * <field id="color" translate="label" type="text" sortOrder="30" showInDefault="1" showInWebsite="1" showInStore="1">
 *     <label>Color</label>
 *     <comment><![CDATA[Background color]]></comment>
 *     <frontend_model>Nwdthemes\Base\Block\System\Config\Form\Field\Color</frontend_model>
 * </field>
 *
 */
class Color extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setData('class', $element->getData('class') . ' jscolor');
        return $element->getElementHtml();
    }

}