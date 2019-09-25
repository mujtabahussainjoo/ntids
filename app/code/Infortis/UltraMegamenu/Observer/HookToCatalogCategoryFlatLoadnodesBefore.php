<?php

namespace Infortis\UltraMegamenu\Observer;

use Infortis\UltraMegamenu\Observer\AbstractObserver;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class HookToCatalogCategoryFlatLoadnodesBefore extends AbstractObserver implements ObserverInterface
{
	public function execute(Observer $observer)
	{
		$columns = [];
		$observer->getSelect()->columns(
			['umm_dd_type', 'umm_dd_width', 
			'umm_dd_proportions', 'umm_dd_columns', 
			'umm_dd_block_top', 'umm_dd_block_bottom', 
			'umm_dd_block_left', 'umm_dd_block_right', 
			'umm_cat_target', 'umm_cat_label']
		);
	}
}
