<?php

namespace Serole\Storesorting\Block\Magento\Backend\Store;


class Switcher extends \Magento\Backend\Block\Store\Switcher
{
	/**
     * Get websites
     *
     * @return \Magento\Store\Model\Website[]
     */
    public function getWebsites()
    {
        $websites = $this->_storeManager->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            $websites = array_intersect_key($websites, array_flip($websiteIds));
        }
		
		usort($websites, array('Serole\Storesorting\Block\Magento\Backend\Store\Switcher','cmp')); 
		
		return $websites;
    }
	
	public static function cmp($a, $b) 
	{
		return strcmp($a->getName(), $b->getName());
	}
	
}
	
	