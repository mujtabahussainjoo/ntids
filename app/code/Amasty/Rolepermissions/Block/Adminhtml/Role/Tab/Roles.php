<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Roles extends \Magento\Backend\Block\Widget\Form\Generic
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

        $fieldset = $form->addFieldset('amrolepermissions_role_fieldset_role', ['legend' => __('Admin Role Access')]);

        $grid = $this->getChildBlock('grid');

        $mode = $fieldset->addField(
            'role_access_mode',
            'select',
            [
                'label'  => __('Allow Access To'),
                'id'     => 'amrolepermissions[role_access_mode]',
                'name'   => 'amrolepermissions[role_access_mode]',
                'values' => [
                    self::MODE_ANY      => __('All User Roles'),
                    self::MODE_SELECTED => __('Selected User Roles')
                ]
            ]
        );

        $fieldset->addField(
            'amasty_role_note',
            'note',
            [
                'label' => __('Note'),
                'text' => __(
                    'This option allows creating admin users with the provided roles. '
                    . 'Please make sure you enable the User management (All users) '
                    . 'and disabled the Role management in the %1 tab.',
                    '<a href="#" onclick="jQuery(\'#role_info_tabs_account\').click(); return false;">'
                    . '"Role Resources"</a>'
                ),
            ]
        );

        $fieldset->addField(
            'user_role_list',
            'hidden',
            [
                'after_element_html' => "<div>{$grid->toHtml()}</div>",
            ]
        );

        $form->addValues($model->getData());
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap($mode->getHtmlId(), $mode->getName())
                ->addFieldMap('amrolepremissions_allowed_role_grid', 'amrolepremissions_allowed_role_grid')
                ->addFieldDependence(
                    'amrolepremissions_allowed_role_grid',
                    $mode->getName(),
                    self::MODE_SELECTED
                )
        );

        return parent::_prepareForm();
    }
}
