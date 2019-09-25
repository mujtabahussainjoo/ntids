<?php

namespace Serole\Serialcode\Block\Adminhtml\Codes\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_systemStore;


    protected $eav;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Eav\Model\Config $eav,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        $this->eav = $eav;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('serialcode_data');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );



        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }


       $status  = array('0' => 'Released','1' => 'Assigned', '2' => 'Invalid');
       $mode = array('auto' => 'auto', 'manual' => 'manual');


        $fieldset->addField(
            'OrderID',
            'text',
            [
                'name' => 'OrderID',
                'label' => __('Order Id'),
                'title' => __('Order ID'),
                'required' => true,

            ]
        );


        $fieldset->addField(
            'sku',
            'text',
            [
                'name' => 'sku',
                'label' => __('Sku'),
                'title' => __('Sku'),
                'required' => true,
            ]
        );


        $fieldset->addField(
            'parentsku',
            'text',
            [
                'name' => 'parentsku',
                'label' => __('Parent Sku'),
                'title' => __('Parent Sku'),
            ]
        );


        $fieldset->addField(
            'SerialNumber',
            'text',
            [
                'name' => 'SerialNumber',
                'label' => __('Serial Code'),
                'title' => __('Serial Code'),
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'ExpireDate',
            'text',
            [
                'name' => 'ExpireDate',
                'label' => __('Expiry Date'),
                'title' => __('Expiry Date'), 
            ]
        );
		
		$fieldset->addField(
            'PIN',
            'text',
            [
                'name' => 'PIN',
                'label' => __('PIN'),
                'title' => __('PIN'),
            ]
        );
		
		
		$fieldset->addField(
            'SecondSerialCode',
            'text',
            [
                'name' => 'SecondSerialCode',
                'label' => __('Second SerialCode'),
                'title' => __('Second SerialCode'),
            ]
        );
		
		$fieldset->addField(
            'URL',
            'text',
            [
                'name' => 'URL',
                'label' => __('URL'),
                'title' => __('URL'),
            ]
        );
		
		$fieldset->addField(
            'StartDate',
            'text',
            [
                'name' => 'StartDate',
                'label' => __('Start Date'),
                'title' => __('Start Date'),
            ]
        );

		$fieldset->addField(
            'Value',
            'text',
            [
                'name' => 'Value',
                'label' => __('Value'),
                'title' => __('Value'),
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => $status,
            ]
        );


        $fieldset->addField(
            'mode',
            'select',
            [
                'name' => 'mode',
                'label' => __('Mode'),
                'title' => __('Mode'),
                'required' => true,
                'values' => $mode,
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Customer Email'),
                'title' => __('Customer Email'),
                'required' => false,
            ]
        );







        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
