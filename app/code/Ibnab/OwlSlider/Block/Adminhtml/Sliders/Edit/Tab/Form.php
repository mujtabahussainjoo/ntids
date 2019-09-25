<?php
namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders\Edit\Tab;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
 implements \Magento\Backend\Block\Widget\Tab\TabInterface {

	const FIELD_NAME_SUFFIX = 'sliders';

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
        $model = $this->_coreRegistry->registry('owlslider_slider');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ibnab_OwlSlider::owlslider_sliders_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('slider_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Primary Informations')]);

        if ($model->getId()) {
            $fieldset->addField('slider_id', 'hidden', ['name' => 'slider_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );
        $fieldset->addField(
            'type',
            'select',
            [
                'label' => __('Slider type'),
                'title' => __('Slider type'),
                'name' => 'type',
                'required' => true,
                'options' => $model->getSliderType(),
                'note' => __('Auto related products available only on product page location.'),
            ]
        );
        $fieldset->addField(
            'location',
            'select',
            [
                'label' => __('Slider location'),
                'title' => __('Slider location'),
                'name' => 'location',
                'required' => false,
//                'options' => Productslider::getSliderLocations()
                'values' => $model->getSliderLocations()
            ]
        );
        $fieldset->addField(
            'singleItem',
            'select',
            [
                'label' => __('Is One Slider'),
                'title' => __('Is One Slider'),
                'name' => 'singleItem',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
         $fieldset->addField(
            'productsNumber',
            'text',
            [
                'name' => 'productsNumber',
                'label' => __('Products number'),
                'title' => __('Products number'),
                'note' => __('Number of products displayed in slider. Max. 20 products.'),
            ]
        );
        $fieldset->addField(
            'productImageview',
            'select',
            [
                'label' => __('Image View Size Type'),
                'title' => __('Image View Size Type'),
                'name' => 'productImageview',
                'required' => false,
                'options' => $model->getSliderImageView(),
                'disabled' => $isElementDisabled
            ]
        );


        $fieldset->addField(
            'displayPrice',
            'select',
            [
                'label' => __('Display price'),
                'title' => __('Display price'),
                'name' => 'displayPrice',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'displayCart',
            'select',
            [
                'label' => __('Display cart'),
                'title' => __('Display add to cart button'),
                'name' => 'displayCart',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'displayWishlist',
            'select',
            [
                'label' => __('Display wishlist'),
                'title' => __('Display add to wish list'),
                'name' => 'displayWishlist',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'displayCompare',
            'select',
            [
                'label' => __('Display compare'),
                'title' => __('Display add to compare'),
                'name' => 'displayCompare',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
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

        $fieldset->addField(
            'excludeFromCart',
            'select',
            [
                'label' => __('Exclude from cart'),
                'title' => __('Exclude from cart'),
                'note'  => __('Don\'t display sliders on cart page'),
                'name' => 'excludeFromCart',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'excludeFromCheckout',
            'select',
            [
                'label' => __('Exclude from checkout'),
                'title' => __('Exclude from cart'),
                'note'  => __('Don\'t display sliders on checkout'),
                'name' => 'exclude_from_checkout',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'showTitle',
            'select',
            [
                'label' => __('Show Title'),
                'title' => __('Show Title'),
                'name' => 'showTitle',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'items',
            'text',
            [
                'name' => 'items',
                'label' => __('Items General'),
                'title' => __('Items General'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'itemsDesktop',
            'text',
            [
                'name' => 'itemsDesktop',
                'label' => __('Items Desktop'),
                'title' => __('Items Desktop'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'itemsDesktopSmall',
            'text',
            [
                'name' => 'itemsDesktopSmall',
                'label' => __('Items Desktop Small'),
                'title' => __('Items Desktop Small'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'itemsTablet',
            'text',
            [
                'name' => 'itemsTablet',
                'label' => __('Items Tablet'),
                'title' => __('Items Tablet'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'itemsMobile',
            'text',
            [
                'name' => 'itemsMobile',
                'label' => __('Items Mobile'),
                'title' => __('Items Mobile'),
                'disabled' => $isElementDisabled
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
        $fieldset->addField(
            'loop',
            'select',
            [
                'label' => __('Loop'),
                'title' => __('Loop'),
                'name' => 'loop',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'center',
            'select',
            [
                'label' => __('Center'),
                'title' => __('Center'),
                'name' => 'center',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lazyLoad',
            'select',
            [
                'label' => __('Lazy Load'),
                'title' => __('Lazy Load'),
                'name' => 'lazyLoad',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'autoplay',
            'select',
            [
                'label' => __('Auto Play'),
                'title' => __('Auto Play'),
                'name' => 'autoplay',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'sortType',
            'select',
            [
                'label' => __('Sort Type'),
                'title' => __('Sort Type'),
                'name' => 'sortType',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'styleSlide',
            'editor',
            [
                'name' => 'styleSlide',
                'label' => __('Style Slide'),
                'title' => __('Style Slide'),
                'disabled' => $isElementDisabled,
                'wysiwyg' => true,
                'required' => false,
            ]
        );
        $fieldset->addField(
            'autoplayTimeout',
            'text',
            [
                'name' => 'autoplayTimeout',
                'label' => __('Auto Play Timeout'),
                'title' => __('Auto Play Timeout'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'nav',
            'select',
            [
                'label' => __('Navigation'),
                'title' => __('Navigation'),
                'name' => 'nav',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'navRewind',
            'select',
            [
                'label' => __('Navigation Rewind'),
                'title' => __('Navigation Rewind'),
                'name' => 'navRewind',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'slideBy',
            'text',
            [
                'label' => __('Slide By'),
                'title' => __('Slide By'),
                'name' => 'slideBy',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'keys',
            'select',
            [
                'label' => __('Enable keyboard'),
                'title' => __('Enable keyboard'),
                'name' => 'keys',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'autoWidth',
            'select',
            [
                'label' => __('Auto Width'),
                'title' => __('Auto Width'),
                'name' => 'autoWidth',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'mouseDrag',
            'select',
            [
                'label' => __('Mouse Drag'),
                'title' => __('Mouse Drag'),
                'name' => 'mouseDrag',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'touchDrag',
            'select',
            [
                'label' => __('Touch Drag'),
                'title' => __('Touch Drag'),
                'name' => 'touchDrag',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'stagePadding',
            'text',
            [
                'name' => 'stagePadding',
                'label' => __('Stage Padding'),
                'title' => __('Stage Padding'),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
            $model->setData('mouseDrag', $isElementDisabled ? '0' : '1');
            $model->setData('touchDrag', $isElementDisabled ? '0' : '1');
            $model->setData('pullDrag', $isElementDisabled ? '0' : '1');
            $model->setData('mergeFit', $isElementDisabled ? '0' : '1');
            $model->setData('navRewind', $isElementDisabled ? '0' : '1');
            $model->setData('dots', $isElementDisabled ? '0' : '1');
            $model->setData('items', $isElementDisabled ? '' : '5');
            $model->setData('itemsDesktop', $isElementDisabled ? '' : '4');
            $model->setData('itemsDesktopSmall', $isElementDisabled ? '' : '3');
            $model->setData('itemsTablet', $isElementDisabled ? '' : '2');
            
            $model->setData('productsNumber', $isElementDisabled ? '' : '7');
            $model->setData('displayTitle', $isElementDisabled ? '0' : '0');
            $model->setData('displayPrice', $isElementDisabled ? '0' : '1');
            $model->setData('displayCart', $isElementDisabled ? '0' : '1');
            $model->setData('displayWishlist', $isElementDisabled ? '0' : '1');
            $model->setData('displayCompare', $isElementDisabled ? '0' : '1');
            $model->setData('excludeFromCart', $isElementDisabled ? '0' : '0');
            $model->setData('excludeFromCheckout', $isElementDisabled ? '0' : '0');
            
            $model->setData('itemsMobile', $isElementDisabled ? '' : '1');
            $model->setData('autoplayTimeout', $isElementDisabled ? '' : '5000');
            $model->setData('slideBy', $isElementDisabled ? '' : '1');
            $model->setData('stagePadding', $isElementDisabled ? '' : '0');
        }
       
       // $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
	}


	public function getPageTitle() {
                if($this->getSlider()){
		return $this->getSlider()->getId() ? __("Edit Slider '%1'", $this->escapeHtml($this->getSlider()->getTitle())) : __('New Slider');}
                 else
                 return __('New Slider');
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
