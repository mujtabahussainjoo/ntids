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

namespace Infortis\Dataporter\Block\Adminhtml\Cfgporter\Export;

use Magento\Backend\Block\Widget\Form\Container;
class Edit extends Container
{
	/**
	 * Constructor
	 */
	public function __construct(
	    \Magento\Backend\Block\Widget\Context $context, 
	    array $data = []	
	)
	{
		parent::__construct($context, $data);

		$this->_blockGroup = 'infortis_dataporter';
		$this->_controller = 'adminhtml_cfgporter_export';
		$this->_headerText = __('Export Configuration');

		$this->updateButton('save', 'label', __('Export Configuration'));
		$this->updateButton('save', 'style', 'background-image:none; border-color:#1986B1; background-color:#1EB5F0;');
		$this->updateButton('save', 'class', '');

		$this->removeButton('back');
		$this->updateButton('reset', 'label', __('Reset Form'));
	}
}
