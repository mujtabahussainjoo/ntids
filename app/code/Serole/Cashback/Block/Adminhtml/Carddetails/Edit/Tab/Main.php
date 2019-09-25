<?php

namespace Serole\Cashback\Block\Adminhtml\Carddetails\Edit\Tab;

/**
 * Carddetails edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Cashback\Model\Status
     */
    protected $_status;
	
	protected $_customer;
    protected $_customerFactory;


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
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Customer $customers,
        \Magento\Store\Model\System\Store $systemStore,
        \Serole\Cashback\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
		$this->_customerFactory = $customerFactory;
        $this->_customer = $customers;
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
        /* @var $model \Serole\Cashback\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('carddetails');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		
        $fieldset->addField(
            'customer_id',
            'select',
            [
                'name' => 'customer_id',
                'label' => __('Customer'),
                'title' => __('Customer'),
				'required' => true,
				'options' => $this->getCustomer(),
                'disabled' => $isElementDisabled
            ]
        );
		
			
						
        $fieldset->addField(
            'card_type',
            'select',
            [
                'label' => __('Card Type'),
                'title' => __('Card Type'),
                'name' => 'card_type',
				'required' => true,
                'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray13(),
                'disabled' => $isElementDisabled
            ]
        );
						
						
        $fieldset->addField(
            'owner_name',
            'text',
            [
                'name' => 'owner_name',
                'label' => __('Card Holder Name'),
                'title' => __('Card Holder Name'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'card_no',
            'text',
            [
                'name' => 'card_no',
                'label' => __('Card No'),
                'title' => __('Card No'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'cvv_no',
            'text',
            [
                'name' => 'cvv_no',
                'label' => __('CVV'),
                'title' => __('CVV'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'issuing_bank',
            'text',
            [
                'name' => 'issuing_bank',
                'label' => __('Issuing Bank'),
                'title' => __('Issuing Bank'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
									
						
        $fieldset->addField(
            'verified',
            'select',
            [
                'label' => __('Verified'),
                'title' => __('Verified'),
                'name' => 'verified',
				'required' => true,
                'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray16(),
                'disabled' => $isElementDisabled
            ]
        );
						
										
						
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Atatus'),
                'title' => __('Atatus'),
                'name' => 'status',
				'required' => true,
                'options' => \Serole\Cashback\Block\Adminhtml\Carddetails\Grid::getOptionArray17(),
                'disabled' => $isElementDisabled
            ]
        );
						
						

        $dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \IntlDateFormatter::MEDIUM
        );

        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created On'),
                'title' => __('Created On'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
				
                'disabled' => $isElementDisabled
            ]
        );
						
						
						

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
	function getCustomer()
	{
		$customerCollection = $this->_customerFactory->create()->getCollection()
                ->addAttributeToSelect("*")
                ->addAttributeToFilter("store_id", array("eq" => "72"))
                ->load();
		$customerData = array();
		foreach($customerCollection as $customer)
		{
			$customerData[$customer->getId()] = $customer->getFirstname()." ".$customer->getLastname()."(".$customer->getEmail().")";
		}
		return $customerData;
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
