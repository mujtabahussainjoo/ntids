<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Block\Adminhtml\Store\Switcher\Form\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Fieldset extends \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'MageWorx_ShippingRules::store/switcher/form/renderer/fieldset.phtml';
}
