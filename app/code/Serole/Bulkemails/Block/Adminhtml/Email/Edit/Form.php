<?php


namespace Serole\Bulkemails\Block\Adminhtml\Email\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic {

    protected $_systemStore;
	
	protected $emailTemplate;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Email\Model\Template $emailTemplate,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
		$this->emailTemplate = $emailTemplate;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _construct(){
        parent::_construct();
        $this->setId('email_form');
        $this->setTitle(__('Bulk Emails Sending'));
    }


    protected function _prepareForm(){
		
		$emailTemplates = array();
        $emailTemplatesCollection = $this->emailTemplate->getCollection();
        foreach ($emailTemplatesCollection->getData() as $emailTemplateItem){
            $emailTemplates[$emailTemplateItem['template_id']] = $emailTemplateItem['template_code'];
        }
		
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post','enctype' => "multipart/form-data"]]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Upload File'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'orderid',
            'text',

            [
                'name' => 'orderid',
                'label' => __('Order ID'),
                'title' => __('Order Id'),
                'required' => true,
                'class' =>  'validate-number',
            ]
        );

        $fieldset->addField(
            'files',
            'file',
            [
                'name' => 'file',
                'label' => __('File'),
                'title' => __('File'),
                'required' => true,
                'note' => 'Upload only CSV format & File size must be < 2M. You can increase limit from php.ini.',
                /*'disabled' => False*/
            ]
        );
		
		 $fieldset->addField(
            'emailtemplateid',
            'select',
            ['name' => 'emailtemplateid',
             'label' => __('Email Template'),
             'title' => __('Email Template'),
             'values' => $emailTemplates,
             'required' => true
            ]
        );

        $fieldset->addType(
            'sampledoc',
            \Serole\Bulkemails\Block\Adminhtml\Sampledoc\Renderer\FileIconAdmin::class
        );

        $fieldset->addField(
            'file',
            'sampledoc',
            [
                'name'  => 'uploadedfile',
                'label' => __('Uploaded File'),
                'title' => __('Uploaded File'),

            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}