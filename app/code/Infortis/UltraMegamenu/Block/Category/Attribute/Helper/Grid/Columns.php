<?php

/**
 * Form textarea element
 */
namespace Infortis\UltraMegamenu\Block\Category\Attribute\Helper\Grid;

use Infortis\Infortis\Lib\Data\Form\Element\Grid\Columns as GridColumns;

class Columns extends GridColumns
{
	protected $_maxColumns = 3;
	protected $_gridUnitMax = 12;
	protected $_labels = ['Left Block', 'Subcategories', 'Right Block'];
}
