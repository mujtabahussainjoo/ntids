<?php
namespace Ibnab\OwlSlider\Block\Adminhtml\Banners\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
 implements \Magento\Backend\Block\Widget\Tab\TabInterface {

	const FIELD_NAME_SUFFIX = 'banners';

	/**
	 * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
	 */
	protected $_fieldFactory;

	/**
	 * @var \Magento\Store\Model\System\Store
	 */
	protected $_systemStore;


	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		//\Magestore\Bannerslider\Helper\Data $bannersliderHelper,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		\Magento\Store\Model\System\Store $systemStore,
		\Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
		array $data = []
	) {
		$this->_localeDate = $context->getLocaleDate();
		$this->_systemStore = $systemStore;
		//$this->_bannersliderHelper = $bannersliderHelper;
		$this->_fieldFactory = $fieldFactory;
		parent::__construct($context, $registry, $formFactory, $data);
	}

	protected function _prepareLayout() {
		$this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
	}

	/**
	 * Prepare form
	 *
	 * @return $this
	 */
	protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('owlslider_banner');
        $modelStore = $this->_coreRegistry->registry('owlslider_banner_store');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ibnab_OwlSlider::owlslider_banners_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('slider_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Informations')]);

        if ($model->getId()) {
            $fieldset->addField('banner_id', 'hidden', ['name' => 'banner_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Slider Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        //var_dump($this->_storeManager->getStore(true)->getId());die();

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
         }

 
        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Click Url'),
                'title' => __('Click Url'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'order',
            'text',
            [
                'name' => 'order',
                'label' => __('Order'),
                'title' => __('Order'),
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'target',
            'select',
            [
                'label' => __('Target'),
                'title' => __('Target'),
                'name' => 'target',
                'options' => $model->getAvailableTargets(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'title' => __('Banner Image'),
                'label' => __('Banner Image'),
                'name' => 'image',
                'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );
        $fieldset->addField(
            'imageAlt',
            'text',
            [
                'name' => 'imageAlt',
                'label' => __('image Alt'),
                'title' => __('image Alt'),
                'disabled' => $isElementDisabled,
            ]
        );

         $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $timeFormat = $this->_localeDate->getTimeFormat(\IntlDateFormatter::SHORT);

        if($model->hasData('start_time')) {
            $datetime = new \DateTime($model->getData('start_time'));
            $model->setData('start_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

        if($model->hasData('end_time')) {
            $datetime = new \DateTime($model->getData('end_time'));
            $model->setData('end_time', $datetime->setTimezone(new \DateTimeZone($this->_localeDate->getConfigTimezone())));
        }

      //  $style = 'color: #000;background-color: #fff; font-weight: bold; font-size: 13px;';
        $fieldset->addField(
            'startTime',
            'date',
            [
                'name' => 'startTime',
                'label' => __('Start time'),
                'title' => __('Start time'),
                'readonly' => true,
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT),
            ]
        );

        $fieldset->addField(
            'endTime',
            'date',
            [
                'name' => 'endTime',
                'label' => __('End time'),
                'title' => __('End time'),
                'readonly' => true,
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'note' => $this->_localeDate->getDateTimeFormat(\IntlDateFormatter::SHORT)
            ]
        );
      
        if (!$model->getId()) {
        }
  
       // $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
	}


	public function getPageTitle() {
                if($this->getSlider()){
		return $this->getSlider()->getId() ? __("Edit Banner '%1'", $this->escapeHtml($this->getSlider()->getTitle())) : __('New Banner');}
                 else
                 return __('New Banner');
	}

	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel() {
		return __('Primary Informations');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle() {
		return __('Primary Informations');
	}

	/**
	 * {@inheritdoc}
	 */
	public function canShowTab() {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isHidden() {
		return false;
	}

	/**
	 * Check permission for passed action
	 *
	 * @param string $resourceId
	 * @return bool
	 */
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed($resourceId);
	}
}
