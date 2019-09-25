<?php

namespace Serole\Partnerreport\Block\Adminhtml\Sales\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{

    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_form');
        $this->setTitle(__('Sales Extract Reports'));
    }


    protected function _prepareForm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$accessibleWebsites = $objectManager->create('\Amasty\Rolepermissions\Model\Rule')->getPartiallyAccessibleWebsites();
        $storeObj = $objectManager->create('\Magento\Store\Model\Website')->getCollection();
        $storeData = array();
          $storeData['ALLSTORES'] = 'ALL STORES';
        foreach ($storeObj->getData() as $storeItem){
			if(in_array($storeItem['website_id'], $accessibleWebsites))
               $storeData[$storeItem['code']] = $storeItem['name'];
        }
		 
		 if(count($storeData) < 20)
			 unset($storeData['ALLSTORES']);
			 
        asort($storeData);
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('partnerreport/salesorder/post'), 'method' => 'post','enctype' => "multipart/form-data"]]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Sales Extract Reports'), 'class' => 'fieldset-wide']
        );

        $reportTimerange =  array('midnight' => 'Midnight to midnight',
                                  'invoice' => 'Match Invoice Time Range' 
                                 );

        $reportDatetype = array('createandupdate' => 'Created and Updated dates',
                                'created' => 'Created date only',
                                'updated' => 'Updated date only'
                               );

        $reportSkiporderrow = array(0 => 'Include order row', 1 => 'Omit order row');

        $reportType = array('sales' => 'Sales Extract',
                            'category' => 'Category Sales Extract'
                            );

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name' => 'from_date',
                'label' => __('From'),
                'date_format' => 'yyyy-MM-dd',
                'required' => true,
                //'time_format' => 'hh:mm:ss'
            ]
        );


        $fieldset->addField(
            'to_date',
            'date',
            [
                'name' => 'to_date',
                'label' => __('To'),
                'date_format' => 'yyyy-MM-dd',
                'required' => true,
                //'time_format' => 'hh:mm:ss'
            ]
        );


        
        $fieldset->addField(
            'report_timerange',
            'select',
            [
                'name' => 'report_timerange',
                'label' => __('Report Type'),
                'title' => __('Report Type'),
                'required' => true,
                'values' => $reportTimerange,
                'disabled' => false,
            ]
        );


        $fieldset->addField(
            'report_datetype',
            'select',
            [
                'name' => 'report_datetype',
                'label' => __('Date/Times to include'),
                'title' => __('Date/Times to include'),
                'required' => true,
                'values' => $reportDatetype,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'report_skiporderrow',
            'select',
            [
                'name' => 'report_skiporderrow',
                'label' => __('Include Order Row?'),
                'title' => __('Include Order Row?   '),
                'required' => true,
                'values' => $reportSkiporderrow,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Websites'),
                    'title' => __('Websites'),
                    'required' => true,
                    'values' => $storeData,
                    'disabled' => false,
                ]
        );


        $fieldset->addField(
            'report_type',
            'select',
            [
                'name' => 'report_type',
                'label' => __('Report Type'),
                'title' => __('Report Type'),
                'required' => true,
                'values' => $reportType,
                'disabled' => false,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}