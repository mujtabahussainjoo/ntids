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

namespace Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;
class Form extends Generic
{
    /**
     * @var StoreManagerInterface
     */
    protected $_modelStoreManagerInterface;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    public function __construct(Context $context, 
        Registry $registry, 
        FormFactory $formFactory, 
        array $data = [], 
        // StoreManagerInterface $modelStoreManagerInterface, 
        Store $systemStore, 
        LayoutFactory $viewLayoutFactory)
    {
        $this->_modelStoreManagerInterface = $context->getStoreManager();
        $this->_systemStore = $systemStore;
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
        // $form = new DataForm(
        //     [
        //         'id'		=> 'edit_form',
        //         'method'	=> 'post',
        //     ]
        // );
        $form = $this->_formFactory->create();
        $form->setId('edit_form');
        $form->setMethod('post');
        
		$fieldset = $form->addFieldset('display', [
			'legend'	=> __('Export settings'),
			'class'		=> 'fieldset-wide'
		]);

		$fieldset->addField('preset_name', 'text', [
			'name'		=> 'preset_name',
			'label'		=> __('File Name'),
			'title'		=> __('File Name'),
			'note'		=> __('This will be the name of the file in which configuration will be saved. You can enter any name you want.'),
			'required'	=> true,
		]);

		$fieldset->addField('modules', 'multiselect', [
			'name'		=> 'modules',
			'label'		=> __('Select Elements of the Configuration to Export'),
			'title'		=> __('Select Elements of the Configuration to Export'),
			'values'	=> ObjectManager::getInstance()->create('Infortis\Dataporter\Model\Source\Cfgporter\Packagemodules')
				->toOptionArray($this->getRequest()->getParam('package')),
			'required'	=> true,
		]);

		//IMPORTANT: allow to select only one store per export
		if (!$this->_modelStoreManagerInterface->isSingleStoreMode()) //Check is single store mode
		{
			$fieldStores = $fieldset->addField('store_id', 'select', [
				'name'		=> 'stores',
				'label'		=> __('Configuration Scope'),
				'title'		=> __('Configuration Scope'),
				'note'		=> __('Configuration of selected store will be saved in a file.'),
				'required'	=> true,
				'values'	=> $this->_systemStore->getStoreValuesForForm(false, true),
			]);
			$renderer = $this->_viewLayoutFactory->create()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
			$fieldStores->setRenderer($renderer);
		}
		else
		{
			$fieldset->addField('store_id', 'hidden', [
				'name'      => 'stores',
				'value'     => $this->_modelStoreManagerInterface->getStore(true)->getId(),
			]);
		}

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
		$actionUrl = $this->getUrl('*/*/export');
		$form->setAction($actionUrl);
		$form->setUseContainer(true);

		$this->setForm($form);
		return parent::_prepareForm();
	}
}