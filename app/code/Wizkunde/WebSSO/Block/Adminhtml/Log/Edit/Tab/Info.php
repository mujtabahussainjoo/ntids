<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Log\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Info extends Generic
{
    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_sso_log');
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(MEQP2.PHP.ProtectedClassMember.FoundProtected)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form_info', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('info_');
        $form->addFieldNameSuffix('info');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Log Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'date',
            'text',
            [
                'name' => 'date',
                'label' => __('Date'),
                'title' => __('Date')
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('Unique Identifier'),
                'title' => __('Unique Identifier')
            ]
        );

        $fieldset->addField(
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status')
            ]
        );

        $fieldset->addField(
            'server',
            'text',
            [
                'name' => 'server',
                'label' => __('Server'),
                'title' => __('Server')
            ]
        );

        $fieldset->addField(
            'mappings',
            'textarea',
            [
                'name' => 'mappings',
                'label' => __('Incoming Data'),
                'title' => __('Incoming Data')
            ]
        );

        $fieldset->addField(
            'additional_info',
            'textarea',
            [
                'name' => 'additional_info',
                'label' => __('Additional Info'),
                'title' => __('Additional Info')
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
