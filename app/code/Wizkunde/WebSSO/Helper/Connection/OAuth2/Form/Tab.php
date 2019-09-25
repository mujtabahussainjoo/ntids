<?php

namespace Wizkunde\WebSSO\Connection\OAuth2\Form;

use Magento\Backend\Block\Widget\Form\Generic;

class Tab extends Generic
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
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form_oauth2', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('oauth2_');
        $form->addFieldNameSuffix('oauth2');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('OAuth2 Settings'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'server_type',
            'select',
            [
                'name' => 'server_type',
                'label' => __('Server Type'),
                'title' => __('Server Type'),
                'required' => false,
                'values' => ['oauth2' => 'OAuth 2.0', 'openid' => 'OpenID'],
            ]
        );

        $fieldset->addField(
            'scope_permissions',
            'text',
            [
                'name' => 'scope_permissions',
                'label' => __('Scope Permissions'),
                'title' => __('Scope Permissions'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'login_url',
            'text',
            [
                'name' => 'login_url',
                'label' => __('Login URL'),
                'title' => __('Login URL'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'logout_url',
            'text',
            [
                'name' => 'logout_url',
                'label' => __('Logout URL'),
                'title' => __('Logout URL'),
                'required' => false,
            ]
        );


        $fieldset->addField(
            'token_endpoint',
            'text',
            [
                'name' => 'token_endpoint',
                'label' => __('Token Endpoint'),
                'title' => __('Token Endpoint'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'userinfo_endpoint',
            'text',
            [
                'name' => 'userinfo_endpoint',
                'label' => __('Userinfo Endpoint'),
                'title' => __('Userinfo Endpoint'),
                'required' => false,
            ]
        );
        $fieldset->addField(
            'client_id',
            'text',
            [
                'name' => 'client_id',
                'label' => __('Client ID'),
                'title' => __('Client ID'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'client_secret',
            'text',
            [
                'name' => 'client_secret',
                'label' => __('Client Secret'),
                'title' => __('Client Secret'),
                'required' => false,
            ]
        );

        $form->setValues($model->getData('type_oauth2'));
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
