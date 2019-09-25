<?php


namespace Serole\StockNotifier\Block\Stock;

class Notice extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
	\Magento\Framework\View\Element\Template\Context $context,
	 \Magento\Framework\Registry $registry,
	 \Serole\Sage\Model\Inventory $stockItemRepository,
	  array $data = []
	){
		$this->_stockItemRepository = $stockItemRepository; 
		$this->_registry = $registry;          	
		parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function stockNotice()
    {

		$currentProduct = $this->_registry->registry('current_product');
		$StockNotifiation = $currentProduct->getStockNotifiation();
		$StockThreshold = $currentProduct->getStockThreshold();
		$isStockProd = $currentProduct->getIsStockItem();
		if($isStockProd && $StockNotifiation)
		{
		    $productStock =$this->_stockItemRepository->getStockQty(array($currentProduct->getSku()));
			if(isset($productStock[$currentProduct->getSku()]['qty']))
			{
				$qty = $productStock[$currentProduct->getSku()]['qty'];
				 if($qty <= $StockThreshold){  
					$msg='<span style="font-weight:bold;">Limited Products ! Only <span style="color:red; font-weight:bold;">'.$qty.'</span> left.</span>';
					return __($msg);
				 }	
				 else{ 
					$msg='<span style="font-weight:bold;">More than <span style="font-weight:bold;">'.$qty.'</span> available.</span>';
					return __($msg);
				 }	
			}
				
		}			

    }
}
