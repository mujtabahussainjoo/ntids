<?php

namespace Serole\Digitalglue\Block\Adminhtml\Digitalglue\Edit\Tab;

/**
 * Digitalglue edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Digitalglue\Model\Status
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
        \Serole\Digitalglue\Model\Status $status,
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
        /* @var $model \Serole\Digitalglue\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('digitalglue');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		
        $fieldset->addField(
            'referencenumber',
            'text',
            [
                'name' => 'referencenumber',
                'label' => __('Order Id'),
                'title' => __('Order Id'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'sku',
            'text',
            [
                'name' => 'sku',
                'label' => __('SKU'),
                'title' => __('SKU'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'magento_sku',
            'text',
            [
                'name' => 'magento_sku',
                'label' => __('Magento SKU'),
                'title' => __('Magento SKU'),
				'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'quantity',
            'text',
            [
                'name' => 'quantity',
                'label' => __('Quantity'),
                'title' => __('Quantity'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'statuscode',
            'text',
            [
                'name' => 'statuscode',
                'label' => __('Status Code'),
                'title' => __('Status Code'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'statusmessage',
            'text',
            [
                'name' => 'statusmessage',
                'label' => __('Status Message'),
                'title' => __('Status Message'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'receiptnumber',
            'text',
            [
                'name' => 'receiptnumber',
                'label' => __('Receipt Number'),
                'title' => __('Receipt Number'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'redemptionurl',
            'textarea',
            [
                'name' => 'redemptionurl',
                'label' => __('Redemption Url'),
                'title' => __('Redemption Url'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'expirydate',
            'text',
            [
                'name' => 'expirydate',
                'label' => __('Expiry Date'),
                'title' => __('Expiry Date'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'costperunit',
            'text',
            [
                'name' => 'costperunit',
                'label' => __('Cost Per Unit'),
                'title' => __('Cost Per Unit'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'rrpperunit',
            'text',
            [
                'name' => 'rrpperunit',
                'label' => __('RRP Per Unit'),
                'title' => __('RRP Per Unit'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'includesgst',
            'text',
            [
                'name' => 'includesgst',
                'label' => __('Includes Gst'),
                'title' => __('Includes Gst'),
				
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
