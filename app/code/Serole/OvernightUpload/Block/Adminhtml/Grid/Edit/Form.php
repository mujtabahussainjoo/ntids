<?php

namespace Serole\OvernightUpload\Block\Adminhtml\Grid\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_systemStore;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Serole\OvernightUpload\Model\Status $options,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );


        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }

        $status = array(0 => "Disable",1 => 'Enable');

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'store_id',
                'label' => __('Associate to Website'),
                'title' => __('Associate to Website'),
                'required' => true,
                'values' => $this->_systemStore->getWebsiteValuesForForm(),

            ]
        );

        $fieldset->addField(
            'partnercode',
            'text',
            [
                'name' => 'partnercode',
                'label' => __('partnercode'),
                'id' => 'title',
                'title' => __('partnercode'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'company_name',
            'text',
            [
                'name' => 'company_name',
                'label' => __('Company Name'),
                'required' => true,
            ]
        );


        $fieldset->addField(
            'server_name',
            'text',
            [
                'name' => 'server_name',
                'label' => __('Server Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'server_port',
            'text',
            [
                'name' => 'server_port',
                'label' => __('Server Port'),
                'required' => true,
            ]
        );


        $fieldset->addField(
            'server_protocol',
            'text',
            [
                'name' => 'server_protocol',
                'label' => __('Server protocol'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'server_username',
            'text',
            [
                'name' => 'server_username',
                'label' => __('Server Username'),
                'required' => true,
            ]
        );



        $fieldset->addField(
            'server_password',
            'text',
            [
                'name' => 'server_password',
                'label' => __('Server Password'),
                'required' => true,
            ]
        );


        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'required' => true,
                'values' => $status,
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
