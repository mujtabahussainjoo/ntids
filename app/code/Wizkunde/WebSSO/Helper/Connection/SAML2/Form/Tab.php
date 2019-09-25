<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Form;

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
            ['data' => ['id' => 'edit_form_saml2', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('saml2_');
        $form->addFieldNameSuffix('saml2');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('SAML2 Information'), 'class' => 'fieldset-wide']
        );

        $certificateFieldset = $form->addFieldset(
            'certificate_fieldset',
            ['legend' => __('SAML2 Certificate'), 'class' => 'fieldset-wide']
        );

        $certCreateFieldset = $form->addFieldset(
            'certcreate_fieldset',
            ['legend' => __('Generate Certificate'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'name_id',
            'text',
            [
                'name' => 'name_id',
                'label' => __('NameID'),
                'title' => __('NameID'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'name_id_format',
            'select',
            [
                'name' => 'name_id_format',
                'label' => __('NameID Format'),
                'title' => __('NameID Format'),
                'required' => false,
                'values' => [
                    'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                    'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
                    'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
                    'urn:oasis:names:tc:SAML:2.0:nameid-format:transient' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'
                ]
            ]
        );

        $fieldset->addField(
            'metadata_url',
            'text',
            [
                'name' => 'metadata_url',
                'label' => __('Metadata URL'),
                'title' => __('Metadata URL'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'is_passive',
            'select',
            [
                'name' => 'is_passive',
                'label' => __('Is Passive'),
                'title' => __('Is Passive'),
                'required' => false,
                'values' => [0 => 'No', 1 => 'Yes']
            ]
        );

        $fieldset->addField(
            'sso_binding',
            'select',
            [
                'name' => 'sso_binding',
                'label' => __('Single Sign-On Binding'),
                'title' => __('Single Sign-On Binding'),
                'required' => false,
                'values' => ['post' => 'HTTP-POST', 'redirect' => 'HTTP-Redirect']
            ]
        );

        $fieldset->addField(
            'slo_binding',
            'select',
            [
                'name' => 'slo_binding',
                'label' => __('Single Logout Binding'),
                'title' => __('Single Logout Binding'),
                'required' => false,
                'values' => ['post' => 'HTTP-POST', 'redirect' => 'HTTP-Redirect']
            ]
        );

        $fieldset->addField(
            'metadata_expiration',
            'select',
            [
                'name' => 'metadata_expiration',
                'label' => __('Metadata Expiration'),
                'title' => __('Metadata Expiration'),
                'required' => false,
                'values' => [
                    '0' => 'Disabled',
                    '86400' => '1 Day',
                    '604800' => '1 Week',
                    '2630000' => '1 Month',
                    '31560000' => '1 Year'
                ]
            ]
        );

        $fieldset->addField(
            'sign_metadata',
            'select',
            [
                'name' => 'sign_metadata',
                'label' => __('Sign SP Metadata'),
                'title' => __('Sign SP Metadata'),
                'required' => false,
                'values' => [0 => 'No', 1 => 'Yes']
            ]
        );

        $fieldset->addField(
            'algorithm',
            'select',
            [
                'name' => 'algorithm',
                'label' => __('Hashing Algorithm'),
                'title' => __('Hashing Algorithm'),
                'required' => false,
                'values' => ['sha256' => 'SHA256', 'sha1' => 'SHA1']
            ]
        );

        $fieldset->addField(
            'forceauthn',
            'select',
            [
                'name' => 'forceauthn',
                'label' => __('Ignore SSO session, always relogin if magento session expires'),
                'title' => __('Ignore SSO session, always relogin if magento session expires'),
                'required' => false,
                'values' => [0 => 'No', 1 => 'Yes']
            ]
        );

        $certificateFieldset->addField(
            'crt_data',
            'textarea',
            [
                'name' => 'crt_data',
                'label' => __('SP Public Certificate'),
                'title' => __('SP Public Certificate'),
                'required' => false,
            ]
        );

        $certificateFieldset->addField(
            'pem_data',
            'textarea',
            [
                'name' => 'pem_data',
                'label' => __('SP Private Certificate'),
                'title' => __('SP Private Certificate'),
                'required' => false,
            ]
        );

        $certificateFieldset->addField(
            'passphrase',
            'text',
            [
                'name' => 'passphrase',
                'label' => __('Certificate passphrase'),
                'title' => __('Certificate passphrase'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'country_name',
            'text',
            [
                'name' => 'country_name',
                'label' => __('Country Code'),
                'title' => __('Country Code'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'state_or_province_name',
            'text',
            [
                'name' => 'state_or_province_name',
                'label' => __('State or Province Name'),
                'title' => __('State or Province Name'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'locality_name',
            'text',
            [
                'name' => 'locality_name',
                'label' => __('Locality Name'),
                'title' => __('Locality Name'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'organization_name',
            'text',
            [
                'name' => 'organization_name',
                'label' => __('Organization Name'),
                'title' => __('Organization Name'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'organizational_unit_name',
            'text',
            [
                'name' => 'organizational_unit_name',
                'label' => __('Organizational Unit Name'),
                'title' => __('Organizational Unit Name'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'common_name',
            'text',
            [
                'name' => 'common_name',
                'label' => __('Domain Name'),
                'title' => __('Domain Name'),
                'required' => false,
            ]
        );

        $certCreateFieldset->addField(
            'email_address',
            'text',
            [
                'name' => 'email_address',
                'label' => __('E-Mail Address'),
                'title' => __('E-Mail Address'),
                'required' => false,
                'after_element_html' => '<button type="button" id="generate" class="button generate-button">Generate Certificate</button>'
            ]
        );

        $form->setValues($model->getData('type_saml2'));
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
