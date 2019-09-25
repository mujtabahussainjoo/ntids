<?php

namespace Serole\Orderreport\Block\Adminhtml\Sales\Edit;

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
        $this->setTitle(__('Sales Orders Reports'));
    }


    protected function _prepareForm()
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
		$stores = $storeManager->getStores(true, false);
        $storeData = array();
        $storeData['ALLSTORES'] = 'ALL STORES';
        foreach ($stores as $storeItem){
			if($storeItem->getId()!=0 && $storeItem->getId()!=1){
				$storeData[$storeItem->getId()] = $storeItem['name'];
			}
        }
		if(count($storeData) < 20)
			 unset($storeData['ALLSTORES']);
		 
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('orderreport/salesorder/post'), 'method' => 'post','enctype' => "multipart/form-data"]]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Sales Orders Reports'), 'class' => 'fieldset-wide']
        );

        $fileTypes = array('pdf' => 'PDF','csv'=>'CSV');

        $orderStatus =  array('all' => 'All',
                              'canceled' => 'Canceled',
                              'closed' => 'Closed',
                              'complete' => 'Complete',
                              'pending' => 'Pending',
                              'processing' => 'Processing',
                              );

        $fieldset->addField(
            'file_type',
            'select',
            [
                'name' => 'file_type',
                'label' => __('File Type'),
                'title' => __('File Type'),
                'required' => true,
                'values' => $fileTypes,
                'disabled' => false,
            ]
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
                'store_id',
                'select',
                [
                    'name' => 'store_id',
                    'label' => __('Choose Website'),
                    'title' => __('Choose Website'),
                    'required' => true,
                    'values' => $storeData, //$this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => false,
                ]
        );


        $fieldset->addField(
            'order_status',
            'select',
            [
                'name' => 'order_status',
                'label' => __('Order Status'),
                'title' => __('Order Status'),
                'required' => true,
                'values' => $orderStatus,
                'disabled' => false,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}