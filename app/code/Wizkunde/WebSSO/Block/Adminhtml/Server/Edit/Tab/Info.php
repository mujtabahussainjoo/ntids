<?php

namespace Wizkunde\WebSSO\Block\Adminhtml\Server\Edit\Tab;

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
        return $this->_coreRegistry->registry('_sso_server');
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
            ['legend' => __('Server Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('Unique Identifier'),
                'title' => __('Unique Identifier'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'connection_type',
            'select',
            [
                'name' => 'connection_type',
                'label' => __('Connection Type'),
                'title' => __('Connection Type'),
                'class' => 'required-entry',
                'required' => true,
                'values' => ['OAuth2' => 'OAuth2', 'SAML2' => 'SAML2']
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
