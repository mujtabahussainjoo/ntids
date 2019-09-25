<?php

namespace Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Edit\Tab;

/**
 * Racparkpass edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Racparkpasses\Model\Status
     */
    protected $_status;
	
	protected $_serialize;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Serole\Racparkpasses\Model\Status $status,
		\Magento\Framework\Serialize\Serializer\Json $serialize,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
		$this->_serialize = $serialize;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Serole\Racparkpasses\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('racparkpass');

        $isElementDisabled = true;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('item_id', 'hidden', ['name' => 'item_id']);
        }

		
        $fieldset->addField(
            'order_number',
            'text',
            [
                'name' => 'order_number',
                'label' => __('Order #'),
                'title' => __('Order #'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
				
        $fieldset->addField(
            'fullname',
            'text',
            [
                'name' => 'fullname',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
				
                'disabled' => $isElementDisabled
            ]
        );
		
		 $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Product'),
                'title' => __('Product'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'veh_reg_1',
            'text',
            [
                'name' => 'veh_reg_1',
                'label' => __('Vehicle Registration 1'),
                'title' => __('Vehicle Registration 1'),
				'required'  => true,
                'disabled' => false
            ]
        );
					
        $fieldset->addField(
            'veh_reg_2',
            'text',
            [
                'name' => 'veh_reg_2',
                'label' => __('Vehicle Registration 2'),
                'title' => __('Vehicle Registration 2'),
				'required'  => false,
                'disabled' => false
            ]
        );
					
       
					
       $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::SHORT
        );

        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'label' => __('Valid From Date'),
                'title' => __('Valid From Date'),
                    'date_format' => 'd/M/yy',
                'disabled' => false
            ]
        );
					

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
		
        $form->setValues($model->getData());
		$form->getElement('order_number')->setValue($this->getRequest()->getParam('order_number'));
        $form->getElement('fullname')->setValue($this->getRequest()->getParam('fullname'));
		if(!empty($model->getProductOptions()) && $model->getProductOptions() != '')
		{
		  $options = $this->_serialize->unserialize($model->getProductOptions());
			if (isset($options['options'])){
				
				foreach($options['options'] as $option){
					
					if ($option['label'] == 'Vehicle Registration Number'){
						$form->getElement('veh_reg_1')->setValue($option['value']);			
						
					} else if ($option['label'] == '2nd Vehicle Registration Number'){
						$form->getElement('veh_reg_2')->setValue($option['value']);			
						
					} else if ($option['label'] == 'Park Pass start date'){
						$form->getElement('start_date')->setValue($option['value']);			
            		}
				}
			}
		}
        $this->setForm($form);
		   // Remove 2nd veh reg for Holiday Pass
			if ($model->getSku() == 'RACHP'){			
				$fieldset->removeField('veh_reg_2');  
			}
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    
    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
}
