<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author		Infortis
 * @copyright	Copyright (c) 2014 Infortis
 * @license		Regular License http://themeforest.net/licenses/regular 
 */

namespace Infortis\Dataporter\Block\Adminhtml\Cfgporter\Import\Edit;

use Infortis\Infortis\Model\Config\Scope;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
class Form extends Generic
{
    /**
     * @var Scope
     */
    protected $_configScope;

    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    public function __construct(Context $context, 
        Registry $registry, 
        FormFactory $formFactory, 
        array $data = [], 
        Scope $configScope, 
        LayoutFactory $viewLayoutFactory)
    {
        $this->_configScope = $configScope;
        $this->_viewLayoutFactory = $viewLayoutFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

	/**
	 * Preparing form
	 *
	 * @return Generic
	 */
	protected function _prepareForm()
	{
// 		$form = new DataForm(
// 			[
// 				'id'		=> 'edit_form',
// 				'method'	=> 'post',
// 				'enctype'	=> 'multipart/form-data'
// 			]
// 		);
        
        $form = $this->_formFactory->create();		
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setEnctype('multipart/form-data');        
		$fieldset = $form->addFieldset('display', [
			'legend'	=> __('Import settings'),
			'class'		=> 'fieldset-wide',
		]);

		$fieldPreset = $fieldset->addField('preset_name', 'select', [
			'name'		=> 'preset_name',
			'label'		=> __('Select Configuration to Import'),
			'title'		=> __('Select Configuration to Import'),
			'required'	=> true,
			'values'	=> ObjectManager::getInstance()->create('Infortis\Dataporter\Model\Source\Cfgporter\Packagepresets')
				->toOptionArray($this->getRequest()->getParam('package')),
		]);

		$fieldDataImportFile = $fieldset->addField('data_import_file', 'file', [
			'name'		=> 'data_import_file',
			'label'		=> __('Select File With Saved Configuration to Import'),
			'title'		=> __('Select File With Saved Configuration to Import'),
			'required'	=> false,
		]);
		//IMPORTANT: allow to select only one store per import
		$fieldStores = $fieldset->addField('store_id', 'select', [
			'name'		=> 'stores',
			'label'		=> __('Configuration Scope'),
			'title'		=> __('Configuration Scope'),
			'note'		=> __("Imported configuration settings will be applied to selected scope (selected store view or website). If you're not sure what is 'scope' in Magento system configuration, it is highly recommended to leave the default scope <strong>'Default Config'</strong>. In this case imported configuration will be applied to all existing store views."),
			'required'	=> true,
			'values'	=> $this->_configScope->getScopeSelectOptions(true, true),
			'value'		=> 'default@0',
		]);
		$renderer = $this->_viewLayoutFactory->create()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
		$fieldStores->setRenderer($renderer);

		/**
		 * Send back the control parameters
		 */
		$fieldset->addField('action_type', 'hidden', [
			'name'  => 'action_type',
			'value' => $this->getRequest()->getParam('action_type'),
		]);

		$fieldset->addField('package', 'hidden', [
			'name'  => 'package',
			'value' => $this->getRequest()->getParam('package'),
		]);

		//Set action and other parameters
		$actionUrl = $this->getUrl('*/*/import');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

        $dependence_block = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
				->addFieldMap($fieldDataImportFile->getHtmlId(), $fieldDataImportFile->getName())
				->addFieldMap($fieldPreset->getHtmlId(), $fieldPreset->getName())
				->addFieldDependence(
					$fieldDataImportFile->getName(),
					$fieldPreset->getName(),
					'upload_custom_file');        
		$this->setChild(
			'form_after',
			$dependence_block
		);
		        
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
