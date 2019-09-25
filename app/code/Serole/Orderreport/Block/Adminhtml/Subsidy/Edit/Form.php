<?php

namespace Serole\Orderreport\Block\Adminhtml\Subsidy\Edit;

use \Magento\Backend\Block\Widget\Form\Generic;

use DateTime;

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
        $this->setId('subsidy_form');
        $this->setTitle(__('Subsidy Invoie Report'));
    }


    protected function _prepareForm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
		$stores = $storeManager->getStores(true, false);
        $storeData = array();
        $storeData['=lease Select Stores'] = 'Please Select Stores';
        foreach ($stores as $storeItem){
			if($storeItem['code']!='default'){
				if($storeItem['code']!='admin'){
					$storeData[$storeItem['code']] = $storeItem['name'];
				}
			}
        }

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getUrl('orderreport/subsidy/post'), 'method' => 'post','enctype' => "multipart/form-data"]]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Subsidy Orders Reports'), 'class' => 'fieldset-wide']
        );

        /*$stores = $this->_systemStore->getStoreValuesForForm(false, true);
        unset($stores[0]);*/
        $timePeriod = array();
        $date = new \DateTime();

        echo '<!-- Date is: '.$date->format('Y-m-d').'-->';
        $date->setISODate($date->format('Y'), $date->format('W'));
        echo '<!-- Date is: '.$date->format('Y-m-d').'-->';
        for ($i =0; $i < 60; $i++) {
            $k = $date->format('Y-W');
            echo '<!-- k is: '.$k.'-->';
            $endDate = new DateTime($date->format('Y-m-d'));
            $endDate->modify('+6 days');
            $k = $endDate->format('Y-W');
            //echo '<!-- k is NOW: '.$k.'-->';
            if ($k == '2016-53'){
                $k = '2015-53';
            }
            if ($i==0){
                $v = 'This week';
            } elseif ($i == 1){
                $v = 'Last week';
            } else {
                $v = 'Week '.$date->format('W');

            }
            $v=$v.' ('.$date->format('d/m/Y').'-'.$endDate->format('d/m/Y').')';
            $timePeriod[$k] = $v;
            //array_push($timePeriod[$key],);
            $date->modify('-7 days');
        }


        $fieldset->addField(
            'period',
            'select',
            [
                'name' => 'period',
                'label' => __('File Type'),
                'title' => __('File Type'),
                'required' => true,
                'values' => $timePeriod,
                'disabled' => false,
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
                'values' => $storeData,
                'disabled' => false,
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}