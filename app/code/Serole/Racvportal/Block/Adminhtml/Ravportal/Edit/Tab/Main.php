<?php

namespace Serole\Racvportal\Block\Adminhtml\Ravportal\Edit\Tab;

/**
 * Ravportal edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Serole\Racvportal\Model\Status
     */
    protected $_status;

    protected $storeFactory;

    protected $region;

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
        \Serole\Racvportal\Model\Status $status,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Directory\Model\ResourceModel\Region\Collection $region,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->storeFactory = $storeFactory;
        $this->region = $region;
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
        /* @var $model \Serole\Racvportal\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('ravportal');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $stores = $this->storeFactory->create()->getCollection()->toOptionArray();
        //echo "<pre>"; print_r($stores);

        
        $regionColl = $this->region->addFieldToFilter('country_id', ['eq' => 'AU']);

        $regionsData = array();
        foreach ($regionColl->getData() as $key => $regionItem){
            $regionsData[$key]['value'] = $regionItem['region_id'];
            $regionsData[$key]['label'] = $regionItem['default_name'];
        }


        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('name'),
                'title' => __('name'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('street'),
                'title' => __('street'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'suburb',
            'text',
            [
                'name' => 'suburb',
                'label' => __('suburb'),
                'title' => __('suburb'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'region',
            'select',
            [
                'name' => 'region',
                'label' => __('region'),
                'title' => __('region'),
                'required'  => true,
                'disabled' => $isElementDisabled,
                'values' => $regionsData
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('postcode'),
                'title' => __('postcode'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );


        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('phone'),
                'title' => __('phone'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('status'),
                'title' => __('status'),
                'required'  => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'store_id',
                'label' => __('Store Id'),
                'title' => __('Store Id'),
                'required'  => true,
                'disabled' => $isElementDisabled,
                'values' => $stores
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
