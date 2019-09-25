<?php

namespace Infortis\Brands\Model\System\Config\Source;

class Linktosearch
{
	public function toOptionArray()
	{
		return [
			['value' => 3,				'label' => __('-- No Link --')],
			['value' => 1,				'label' => __('Quick Search Results')],
			['value' => 2,				'label' => __('Advanced Search Results')],
			['value' => 0,				'label' => __('Custom Page (more options...)')],
		];
	}
}