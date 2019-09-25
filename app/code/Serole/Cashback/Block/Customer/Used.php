<?php

namespace Serole\Cashback\Block\Customer;


class Used extends \Magento\Framework\View\Element\Template {
	
	protected $_usedpointsFactory;
	
	protected $_resource;
	

    public function __construct(
	               \Magento\Catalog\Block\Product\Context $context,
				   \Magento\Framework\App\ResourceConnection $resource,
                   \Serole\Cashback\Model\UsedpointsFactory $UsedpointsFactory,			   
				   array $data = []
				 ) {
                $this->_usedpointsFactory = $UsedpointsFactory;
				$this->_resource = $resource;
                parent::__construct($context, $data);

    }
	
	public function getAllUsedDetails()
	{
		     
		    $om = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $om->create('Serole\Cashback\Model\Customerorder');
            $customerSession = $om->create('Magento\Customer\Model\Session');
		    $customer_id = $customerSession->getCustomer()->getId();
		   
				    return $collection = $this->_usedpointsFactory->create()->getCollection()
		                     ->addFieldToSelect("*")
                             ->addFieldToFilter("customer_id", array("eq" => $customer_id));
					 
	}
	
	public function getOrderEntityId($id)
	{
			$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		    $id = $connection->fetchOne("SELECT entity_id  FROM sales_order where increment_id='$id'");
			return $id;
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