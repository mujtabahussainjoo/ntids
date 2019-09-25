<?php

namespace Infortis\UltraMegamenu\Model\Category\Attribute\Source\Dropdown;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Type
	extends AbstractSource
{
	protected $_options;

	/**
	 * Get list of types
	 */
	public function getAllOptions()
	{
		if (!$this->_options)
		{
			$this->_options = [
				['value' => 0,			'label' => '-'],
				['value' => 1,			'label' => 'Mega drop-down'],
				['value' => 2,			'label' => 'Classic drop-down'],
				['value' => 3,			'label' => 'Simple submenu (no drop-down)'],
			];
		}
		return $this->_options;
	}
}
