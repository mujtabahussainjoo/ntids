<?php

namespace Serole\Copytostores\Controller\Magento\Catalog\Adminhtml\Product;


class Save extends \Magento\Catalog\Controller\Adminhtml\Product\Save
{
	    /**
     * Stop copying data to stores
     *
     */
    protected function copyToStores($data, $productId)
    {
       return;
    }
	
}
	
	