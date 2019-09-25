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
class ManualLink extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \MagePsycho\RedirectPro\Helper\Data $helper
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
        $element->setValue($this->notesText());
        return $element->getValue();
    }

    /**
     * @return String
     */
    protected  function notesText()
    {
        return '<a href="#" class="manual-popup" id="manual-popup">' .
                __('View Notes on Redirection Url')
                . '</a>';
    }
}