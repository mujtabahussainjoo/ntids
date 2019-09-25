<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Attributes extends \Magento\Backend\Block\Widget\Form\Generic
{
    const MODE_ANY = 0;
    const MODE_SELECTED = 1;

    protected function _prepareForm()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('amrolepermissions_attributes_fieldset', ['legend' => __('Attributes Access')]);

        $grid = $this->getChildBlock('grid');

        $mode = $fieldset->addField('attribute_access_mode', 'select', [
            'label' => __('Allow Access To'),
            'id'    => 'amrolepermissions[attribute_access_mode]',
            'name'  => 'amrolepermissions[attribute_access_mode]',
            'values'=> [
                self::MODE_ANY => __('All Attributes'),
                self::MODE_SELECTED => __('Selected Attributes'),
            ]
        ]);

        $fieldset->addField('attributes_list', 'hidden', [
            'after_element_html' => "<div>{$grid->toHtml()}</div>",
        ]);

        $form->addValues($model->getData());
        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap('amrolepremissions_product_attributes_grid', 'amrolepremissions_product_attributes_grid')
            ->addFieldDependence(
                'amrolepremissions_product_attributes_grid',
                $mode->getName(),
                self::MODE_SELECTED
            )
        );

        return parent::_prepareForm();
    }
}
