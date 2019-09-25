<?php

namespace Serole\OvernightUpload\Block\Adminhtml\Providergrid\Edit;


class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected $_systemStore;


    protected $eav;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Serole\OvernightUpload\Model\Status $options,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Eav\Model\Config $eav,
        array $data = []
    ) {
        $this->_options = $options;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        $this->eav = $eav;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('providerrow_data');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );

        $partnerCodeAttribute = $this->eav->getAttribute('catalog_product', 'partnercode');
        $partnerCodeOptions = $partnerCodeAttribute->getSource()->getAllOptions();

        $providerCodeAttribute = $this->eav->getAttribute('catalog_product', 'provider');
        $providerCodeOptions = $providerCodeAttribute->getSource()->getAllOptions();

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
        unset($providerCodeOptions[0]);

        $providerList  = array();
        foreach ($providerCodeOptions as $key => $providerItem){
            $providerItem['value'] = $providerItem['label'];
            $providerList[] = $providerItem;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $partnerObj = $objectManager->create('\Serole\OvernightUpload\Model\Grid')->getCollection();

        /*$partnerList  = array();
        foreach ($partnerCodeOptions as $key => $partnerItem){
            $partnerItem['value'] = $partnerItem['label'];
            $partnerList[] = $partnerItem;
        }*/

        $partnerList  = array();
        foreach ($partnerObj as $key => $partnerItem){
            $partnerList[$key]['label'] = $partnerItem->getPartnercode(); //company_name
            $partnerList[$key]['value'] = $partnerItem->getPartnercode(); //company_name
        }


        $fieldset->addField(
            'providerid',
            'select',
            [
                'name' => 'providerid',
                'label' => __('Provider'),
                'title' => __('Provider'),
                'required' => true,
                'values' => $providerList,

            ]
        );


        $fieldset->addField(
            'patner_groupid',
            'select',
            [
                'name' => 'patner_groupid',
                'label' => __('Partner Group'),
                'title' => __('Partner Group'),
                'required' => true,
                'values' => $partnerList,

            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
