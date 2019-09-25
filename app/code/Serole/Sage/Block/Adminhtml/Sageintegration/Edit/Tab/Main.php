<?php

namespace Serole\Sage\Block\Adminhtml\Sageintegration\Edit\Tab;

/**
 * Sageintegration edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Sage\Model\Status
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
        \Serole\Sage\Model\Status $status,
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
        /* @var $model \Serole\Sage\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('sageintegration');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		
        $fieldset->addField(
            'orderid',
            'text',
            [
                'name' => 'orderid',
                'label' => __('Order Id'),
                'title' => __('Order Id'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'sales_header',
            'text',
            [
                'name' => 'sales_header',
                'label' => __('Header(order)'),
                'title' => __('Header(order)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'sales_details',
            'text',
            [
                'name' => 'sales_details',
                'label' => __('Items(order)'),
                'title' => __('Items(order)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'sales_serialcode',
            'text',
            [
                'name' => 'sales_serialcode',
                'label' => __('Serialcodes(order)'),
                'title' => __('Serialcodes(order)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'payment_receipt',
            'text',
            [
                'name' => 'payment_receipt',
                'label' => __('Payment(order)'),
                'title' => __('Payment(order)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'credit_memo_header',
            'text',
            [
                'name' => 'credit_memo_header',
                'label' => __('Header(CM)'),
                'title' => __('Header(CM)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'credit_memo_details',
            'text',
            [
                'name' => 'credit_memo_details',
                'label' => __('Items(CM)'),
                'title' => __('Items(CM)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'credit_memo_serialcode',
            'text',
            [
                'name' => 'credit_memo_serialcode',
                'label' => __('Serialcodes(CM)'),
                'title' => __('Serialcodes(CM)'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'credit_memo_receipt',
            'text',
            [
                'name' => 'credit_memo_receipt',
                'label' => __('Payment(CM)'),
                'title' => __('Payment(CM)'),
				
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
                'label' => __('Created Date'),
                'title' => __('Created Date'),
                'date_format' => $dateFormat,
                    //'time_format' => $timeFormat,
				'required' => true,
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
