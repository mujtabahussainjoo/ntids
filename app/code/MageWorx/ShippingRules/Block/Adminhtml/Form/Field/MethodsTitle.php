<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Block\Adminhtml\Form\Field;

class MethodsTitle extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Methods
     */
    protected $_methodsRenderer;

    /**
     * Retrieve shipping methods column renderer
     *
     * @return Methods
     */
    protected function _getMethodsRenderer()
    {
        if (!$this->_methodsRenderer) {
            $this->_methodsRenderer = $this->getLayout()->createBlock(
                \MageWorx\ShippingRules\Block\Adminhtml\Form\Field\Methods::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_methodsRenderer->setClass('methods_select');
        }
        return $this->_methodsRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'methods_id',
            ['label' => __('Shipping Method'), 'renderer' => $this->_getMethodsRenderer()]
        );
        $this->addColumn('title', ['label' => __('Title')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Title');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getMethodsRenderer()->calcOptionHash($row->getData('methods_id'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
