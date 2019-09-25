<?php

namespace MagePsycho\GroupSwitcherPro\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 *  @category MagePsycho
 *  @package  MagePsycho_GroupSwitcherPro
 *  @author   Raj KB <magepsycho@gmail.com>
 *  @website  http://www.magepsycho.com
 *  @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    const EXTENSION_URL = 'http://www.magepsycho.com';

    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    protected $groupSwitcherProHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper,
        array $data = []
    ) {
        $this->groupSwitcherProHelper = $groupSwitcherProHelper;
        parent::__construct($context, $data);
    }

    /**
     * Html content return
     * 
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $extensionVersion = $this->groupSwitcherProHelper->getExtensionVersion();
        $extensionTitle   = 'Group Selector Pro';
        $versionLabel     = sprintf(
            '<a href="%s" title="%s" target="_blank">%s</a>',
            self::EXTENSION_URL,
            $extensionTitle,
            $extensionVersion
        );
        $element->setValue($versionLabel);

        return $element->getValue();
    }
}