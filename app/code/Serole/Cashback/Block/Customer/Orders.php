<?php

namespace Serole\Cashback\Block\Customer;


class Orders extends \Magento\Framework\View\Element\Template {
	
	protected $_customerorderFactory;
	
	protected $_productRepositoryFactory;

    public function __construct(
	               \Magento\Catalog\Block\Product\Context $context,
                   \Serole\Cashback\Model\CustomerorderFactory $CustomerorderFactory,
                   \Magento\Catalog\Api\ProductRepositoryInterfaceFactory $productRepositoryFactory,			   
				   array $data = []
				 ) {
                $this->_customerorderFactory = $CustomerorderFactory;
				$this->_productRepositoryFactory = $productRepositoryFactory;
                parent::__construct($context, $data);

    }
	
	public function getAllOrders()
	{
		     
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
		   
				    return $collection = $this->_customerorderFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id))
							 ->setOrder('created_at','DESC');
					 
	}
	
	public function getImgUrl($prodId)
	{
		$objectManager =\Magento\Framework\App\ObjectManager::getInstance();
		$helperImport = $objectManager->get('Infortis\Infortis\Helper\Image');
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($prodId);
		return $helperImport->getImageUrl($product, "category_page_list", 50, 50);
		//return $imageUrl;
	}

	

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

}