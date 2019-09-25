<?php

namespace Serole\ExternalProduct\Block\Adminhtml\Referral\Edit\Tab;

/**
 * Referral edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\ExternalProduct\Model\Status
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
        \Serole\ExternalProduct\Model\Status $status,
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
        /* @var $model \Serole\ExternalProduct\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('referral');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

		/*
        $fieldset->addField(
            'websiteid',
            'text',
            [
                'name' => 'websiteid',
                'label' => __('Website Id'),
                'title' => __('Website Id'),
				
                'disabled' => $isElementDisabled
            ]
        );
		*/			
        $fieldset->addField(
            'customerid',
            'text',
            [
                'name' => 'customerid',
                'label' => __('Customer Id'),
                'title' => __('Customer Id'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'productid',
            'text',
            [
                'name' => 'productid',
                'label' => __('Product Id'),
                'title' => __('Product Id'),
				
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addField(
            'linkurl',
            'text',
            [
                'name' => 'linkurl',
                'label' => __('Link URL'),
                'title' => __('Link URL'),
				
                'disabled' => $isElementDisabled
            ]
        );
									
						
        $fieldset->addField(
            'store',
            'select',
            [
                'label' => __('Store'),
                'title' => __('Store'),
                'name' => 'store',
				
                'options' => \Serole\ExternalProduct\Block\Adminhtml\Referral\Grid::getOptionArray5(),
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
            'date_clicked',
            'date',
            [
                'name' => 'date_clicked',
                'label' => __('Clicked date'),
                'title' => __('Clicked date'),
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
