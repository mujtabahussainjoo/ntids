<?php

namespace MagePsycho\RedirectPro\Block\Adminhtml\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GroupSelectorInfo extends \Magento\Backend\Block\Template implements RendererInterface
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $info = '<div style="border: 1px solid #D6D6D6;padding:0px 4px 4px 4px;margin:20px 4px 4px 4px;background-color:white"><div style="padding:5px">
Custom Redirect Pro bundles another extension <a href="http://www.magepsycho.com/magento2-customer-group-selector-switcher-pro.html" title="Magento 2 Customer Group Selector / Switcher" target="_blank">Magento 2 Group Selector / Switcher</a> for FREE. It allows users to select/switch to their required customer group at registration, using group drop-down or group code.<br /> In order to customize this feature please go to <a href="' . $this->getUrl('adminhtml/system_config/edit/section/magepsycho_groupswitcherpro') . '" title="Manage Customer Group Selector Settings">Stores > Configuration > MagePsycho Extensions > Group Selector Pro</a> section.
</div></div>';
        return sprintf(
            '<tr id="row_%s"><td colspan="5"><div class="section-config"><div class="entry-edit-head admin__collapsible-block">%s</div></div></td></tr>',
            $element->getHtmlId(),
            $info
        );
    }
}