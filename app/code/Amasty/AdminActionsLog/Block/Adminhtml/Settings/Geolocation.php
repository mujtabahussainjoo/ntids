<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */

namespace Amasty\AdminActionsLog\Block\Adminhtml\Settings;

class Geolocation extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\AdminActionsLog\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDisabled(true);

        if ($this->_helper->canUseGeolocation()) {
            $element->setDisabled(false);
        }

        return parent::_getElementHtml($element);
    }
}
