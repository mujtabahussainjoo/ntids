<?php

namespace Serole\Subscriber\Block\Adminhtml\Subscriber\Edit\Tab;

/**
 * Subscriber edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Subscriber\Model\Status
     */
    protected $_status;

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
        \Serole\Subscriber\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
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
        /* @var $model \Serole\Subscriber\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('subscriber');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		
        $fieldset->addField(
            'id',
            'text',
            [
                'name' => 'id',
                'label' => __('Id'),
                'title' => __('Id'),
				
                'disabled' => 'true'
            ]
        );
					
        $fieldset->addField(
            'customer_id',
            'text',
            [
                'name' => 'customer_id',
                'label' => __('Customer Id'),
                'title' => __('Customer Id'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_email',
            'text',
            [
                'name' => 'customer_email',
                'label' => __('Email'),
                'title' => __('Email'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_member',
            'text',
            [
                'name' => 'customer_member',
                'label' => __('Member No'),
                'title' => __('Member No'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_first_name',
            'text',
            [
                'name' => 'customer_first_name',
                'label' => __('Customer Name'),
                'title' => __('Customer Name'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_last_name',
            'text',
            [
                'name' => 'customer_last_name',
                'label' => __('Customer Last Name'),
                'title' => __('Customer Last Name'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_phno',
            'text',
            [
                'name' => 'customer_phno',
                'label' => __('Contact No'),
                'title' => __('Contact No'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_address',
            'text',
            [
                'name' => 'customer_address',
                'label' => __('Address'),
                'title' => __('Address'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_postcode',
            'text',
            [
                'name' => 'customer_postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'customer_state',
            'text',
            [
                'name' => 'customer_state',
                'label' => __('State'),
                'title' => __('State'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'suburb',
            'text',
            [
                'name' => 'suburb',
                'label' => __('Suburb'),
                'title' => __('Suburb'),
				
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
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'export',
            'text',
            [
                'name' => 'export',
                'label' => __('Export'),
                'title' => __('Export'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'opt_in',
            'text',
            [
                'name' => 'opt_in',
                'label' => __('Opt In'),
                'title' => __('Opt In'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'store_id',
            'text',
            [
                'name' => 'store_id',
                'label' => __('Store Id'),
                'title' => __('Store Id'),
				
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
                'label' => __('Created At'),
                'title' => __('Created At'),
                    'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
				
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
            'updted_at',
            'date',
            [
                'name' => 'updted_at',
                'label' => __('Updated At'),
                'title' => __('Updated At'),
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
