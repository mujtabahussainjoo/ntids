<?php

namespace Infortis\Infortis\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Color extends Field
{
    const FIELD_SELECTOR_ATTRIBUTE = 'data-octarine';
    const FIELD_SELECTOR_CLASS = 'octarine';

    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Add color picker
     *
     * @param AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addCustomAttribute(self::FIELD_SELECTOR_ATTRIBUTE, '1');
        $element->addClass(self::FIELD_SELECTOR_CLASS);
        return $element->getElementHtml();
    }
}
