<?php

namespace Serole\Cashback\Block\Adminhtml\Customerorder\Edit\Tab;

/**
 * Customerorder edit form main tab
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
        $model = $this->_coreRegistry->registry('customerorder');

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
				'onchange' => "getCustomerCard()",
                'disabled' => $isElementDisabled 
            ]
        );
					
        $fieldset->addField(
            'merchant_id',
            'select',
            [
                'name' => 'merchant_id',
                'label' => __('Merchant'),
                'title' => __('Merchant'),
				'required' => true,
				'options' => $this->getMerchant(),
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'card_id',
            'select',
            [
                'name' => 'card_id',
                'label' => __('Customer Card'),
                'title' => __('Customer Card'),
				'required' => true,
				'options' => $this->getCards(),
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'order_id',
            'text',
            [
                'name' => 'order_id',
                'label' => __('Order Id'),
                'title' => __('Order Id'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'ship_to',
            'text',
            [
                'name' => 'ship_to',
                'label' => __('Shipping Address'),
                'title' => __('Shipping Address'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'bill_to',
            'text',
            [
                'name' => 'bill_to',
                'label' => __('Billing Address'),
                'title' => __('Billing Address'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'products',
            'text',
            [
                'name' => 'products',
                'label' => __('Products'),
                'title' => __('Products'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'order_total',
            'text',
            [
                'name' => 'order_total',
                'label' => __('Order Total'),
                'title' => __('Order Total'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'rewards_points',
            'text',
            [
                'name' => 'rewards_points',
                'label' => __('Earned Points'),
                'title' => __('Earned Points'),
				'required' => true,
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
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
		
		 $Lastfield = $form->getElement('created_at');
 			
		 $Lastfield->setAfterElementHtml('<script>
		 function getCustomerCard() {
			require([ "jquery","jquery/ui"], function($){
					var param = $("#edit_form").serialize();
					
						$.ajax({
							showLoader: true,
							url: "/ni_admin/cashback/customerorder/ajax/",
							data: param,
							type: "POST"
						}).done(function (data) {
							$("#page_card_id").html(data);
							return true;
						}); 		
				});
			}
			</script>');			
						

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
  protected function getCustomer()
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
	
	protected function getMerchant()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$cateinstance = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
		$cateid = '188';
		$allcategoryproduct = $cateinstance->create()->setStoreId(72)->load($cateid)->getProductCollection()
		->addAttributeToSelect('*'); 
		$prodData = array();
		foreach($allcategoryproduct as $prod)
		{
			$prodData[$prod->getId()] = $prod->getName();
		}
		return $prodData;
	}
	
	protected function getCards()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$cardinstance = $objectManager->create('Serole\Cashback\Model\CarddetailsFactory');
		
		$allcards = $cardinstance->create()->getCollection(); 
		
		$cardData = array();
		return $cardData[] = "Select Card";
		foreach($allcards as $card)
		{
			$cardData[$card->getId()] = $card->getCardNo();
		}
		return $cardData;
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
?>