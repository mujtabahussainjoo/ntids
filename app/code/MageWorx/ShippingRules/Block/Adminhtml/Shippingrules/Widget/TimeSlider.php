<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Shippingrules\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;
use MageWorx\ShippingRules\Model\Rule;

class TimeSlider extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface,
    \Magento\Widget\Block\BlockInterface
{

    const TIME_NAME_FROM = 'time_from';
    const TIME_NAME_TO = 'time_to';

    /**
     * Form element which re-rendering
     *
     * @var \Magento\Framework\Data\Form\Element\Fieldset
     */
    protected $element;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var string
     */
    protected $_template = 'MageWorx_ShippingRules::form/renderer/timeslider.phtml';

    /**
     * @var string
     */
    protected $_htmlId = 'time-range';

    /**
     * Retrieve an element
     *
     * @return \Magento\Framework\Data\Form\Element\Fieldset
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Render element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        return $this->toHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getTemplate()) {
            return '';
        }
        return $this->fetchView($this->getTemplateFile());
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->_htmlId;
    }

    /**
     * @return string
     */
    public function getNameFrom()
    {
        return self::TIME_NAME_FROM;
    }

    /**
     * @return string
     */
    public function getNameTo()
    {
        return self::TIME_NAME_TO;
    }

    /**
     * @param Rule $rule
     * @return $this
     */
    public function setRule(Rule $rule)
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    public function minutesToTime($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        $part = $hours >= 12 ? 'PM' : 'AM';
        return sprintf('%02d:%02d %s', $hours, $minutes, $part);
    }
}
