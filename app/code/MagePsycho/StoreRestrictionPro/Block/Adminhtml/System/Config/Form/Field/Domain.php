<?php
namespace MagePsycho\StoreRestrictionPro\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Domain extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Data
     */
    protected $storeRestrictionProHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper
    ) {
        $this->storeRestrictionProHelper = $storeRestrictionProHelper;
        parent::__construct($context);
    }


    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $domain = $this->storeRestrictionProHelper->getDomainFromSystemConfig();
        $element->setValue($domain);

        return $element->getValue();
    }
}