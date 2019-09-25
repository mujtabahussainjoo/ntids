<?php
namespace Ibnab\OwlSlider\Block\Adminhtml\Sliders\Edit\Tab;

class Second extends \Magento\Backend\Block\Widget\Form\Generic
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Secondary Informations')]);

        $fieldset->addField(
            'pullDrag',
            'select',
            [
                'label' => __('Pull Drag'),
                'title' => __('Pull Drag'),
                'name' => 'pullDrag',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'freeDrag',
            'select',
            [
                'label' => __('Free Drag'),
                'title' => __('Free Drag'),
                'name' => 'freeDrag',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'merge',
            'select',
            [
                'label' => __('Merge'),
                'title' => __('Merge'),
                'name' => 'merge',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'mergeFit',
            'select',
            [
                'label' => __('Merge Fit'),
                'title' => __('Merge Fit'),
                'name' => 'mergeFit',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'URLhashListener',
            'select',
            [
                'label' => __('URL Hash Listener'),
                'title' => __('URL Hash Listener'),
                'name' => 'URLhashListener',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dots',
            'select',
            [
                'label' => __('Dots'),
                'title' => __('Dots'),
                'name' => 'dots',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dotsEach',
            'select',
            [
                'label' => __('Dots Each'),
                'title' => __('Dots Each'),
                'name' => 'dotsEach',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dotData',
            'select',
            [
                'label' => __('Dot Data'),
                'title' => __('Dot Data'),
                'name' => 'dotData',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'autoplayHoverPause',
            'select',
            [
                'label' => __('Auto play Hover Pause'),
                'title' => __('Auto play Hover Pause'),
                'name' => 'autoplayHoverPause',
                'required' => true,
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'navText',
            'text',
            [
                'name' => 'navText',
                'label' => __('Navigation Text'),
                'title' => __('Navigation Text'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'smartSpeed',
            'text',
            [
                'name' => 'smartSpeed',
                'label' => __('Smart Speed'),
                'title' => __('Smart Speed'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'autoplaySpeed',
            'text',
            [
                'name' => 'autoplaySpeed',
                'label' => __('Auto Play Speed'),
                'title' => __('Auto Play Speed'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'navSpeed',
            'text',
            [
                'name' => 'navSpeed',
                'label' => __('Navigation Speed'),
                'title' => __('Navigation Speed'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dotsSpeed',
            'text',
            [
                'name' => 'dotsSpeed',
                'label' => __('Dots Speed'),
                'title' => __('Dots Speed'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dragEndSpeed',
            'text',
            [
                'name' => 'dragEndSpeed',
                'label' => __('DragEnd Speed'),
                'title' => __('DragEnd Speed'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'responsiveRefreshRate',
            'text',
            [
                'name' => 'responsiveRefreshRate',
                'label' => __('Responsive Refresh Rate'),
                'title' => __('Responsive Refresh Rate'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'videoHeight',
            'text',
            [
                'name' => 'videoHeight',
                'label' => __('Video Height'),
                'title' => __('Video Height'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'videoWidth',
            'text',
            [
                'name' => 'videoWidth',
                'label' => __('Video Width'),
                'title' => __('Video Width'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'animateOut',
            'text',
            [
                'name' => 'animateOut',
                'label' => __('Animate Out'),
                'title' => __('Animate Out'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'animateIn',
            'text',
            [
                'name' => 'animateIn',
                'label' => __('Animate In'),
                'title' => __('Animate In'),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('navText', $isElementDisabled ? '' : "['&#x27;next&#x27;','&#x27;prev&#x27;']");
            $model->setData('smartSpeed', $isElementDisabled ? '' : '250');
            $model->setData('autoplaySpeed', $isElementDisabled ? '' : '0');
            $model->setData('navSpeed', $isElementDisabled ? '' : '0');
            $model->setData('dotsSpeed', $isElementDisabled ? '' : '0');
            $model->setData('dragEndSpeed', $isElementDisabled ? '' : '0');
            $model->setData('responsiveRefreshRate', $isElementDisabled ? '' : '200');
            $model->setData('videoHeight', $isElementDisabled ? '' : '0');
            $model->setData('videoWidth', $isElementDisabled ? '' : '0');
            $model->setData('animateOut', $isElementDisabled ? '' : '0');
            $model->setData('animateIn', $isElementDisabled ? '' : '0');
        }
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
	}



	/**
	 * Prepare label for tab
	 *
	 * @return string
	 */
	public function getTabLabel() {
		return __('Secondary Informations');
	}

	/**
	 * Prepare title for tab
	 *
	 * @return string
	 */
	public function getTabTitle() {
		return __('Secondary Informations');
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
