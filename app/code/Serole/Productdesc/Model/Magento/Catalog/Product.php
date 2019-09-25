<?php
namespace Serole\Productdesc\Model\Magento\Catalog;

use Magento\Cms\Model\Template\FilterProvider;

class Product extends \Magento\Catalog\Model\Product
{
	
	public function getShortDescription()
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$filterProvider = $om->create('Magento\Cms\Model\Template\FilterProvider');
		return $filteredDescription = $filterProvider->getBlockFilter()
            ->filter($this->getData('short_description'));

    }
	
	public function getDescription()
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$filterProvider = $om->create('Magento\Cms\Model\Template\FilterProvider');
		return $filteredDescription = $filterProvider->getBlockFilter()
            ->filter($this->getData('description'));

    }
	
}
	
	