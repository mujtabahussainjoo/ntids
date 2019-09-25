<?php
namespace Serole\GiftMessage\Block\Adminhtml\Post\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

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


    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_form');
        $this->setTitle(__('Gift Message Information'));
    }


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('blog_post');
        $emailTemplates = array();
        $emailTemplatesCollection = $this->emailTemplate->getCollection();
        foreach ($emailTemplatesCollection->getData() as $emailTemplateItem){
            $emailTemplates[$emailTemplateItem['template_id']] = $emailTemplateItem['template_subject'];
        }

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post','enctype' => "multipart/form-data"]]
        );

        $form->setHtmlIdPrefix('post_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
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
     if ($model->getId()) {
         $fieldset->addField(
             'image',
             'file',
             [
                 'name' => 'image',
                 'label' => __('Image'),
                 'title' => __('Image'),
                 'required' => false,
                 'note' => 'File size must be < 2M. You can increse limit from php.ini.',
             ]
         );

         $fieldset->addType(
             'uploadedfile',
             '\Serole\GiftMessage\Block\Adminhtml\Post\Renderer\File'
         );

         $fieldset->addField(
             'file',
             'uploadedfile',
             [
                 'name'  => 'uploadedfile',
                 'label' => __('Uploaded File'),
                 'title' => __('Uploaded File'),

             ]
         );

     }else{
         $fieldset->addField(
             'image',
             'file',
             [
                 'name' => 'image',
                 'label' => __('Image'),
                 'title' => __('Image'),
                 'required' => true,
                 'note' => 'File size must be < 2M. You can increse limit from php.ini.',
             ]
         );
     }
    
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
