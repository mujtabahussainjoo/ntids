<?php

namespace MagePsycho\RedirectPro\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @category   MagePsycho
 * @package    MagePsycho_RedirectPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    const EXTENSION_URL = 'http://www.magepsycho.com';

    /**
     * @var \MagePsycho\RedirectPro\Helper\Data
     */
    protected $redirectProHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MagePsycho\RedirectPro\Helper\Data     $redirectProHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MagePsycho\RedirectPro\Helper\Data $redirectProHelper
    ) {
        $this->redirectProHelper = $redirectProHelper;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $extensionVersion = $this->redirectProHelper->getExtensionVersion();
        $extensionTitle   = 'Custom Redirect Pro';
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