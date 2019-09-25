<?php
namespace Serole\Ordercolumn\Ui\Component\Listing\Column;

use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
 
class Shopname extends Column
{
	protected  $_resource;
 
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Magento\Framework\App\ResourceConnection $resource,
        array $components = [], array $data = [])
    {
		$this->_resource = $resource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    public function prepareDataSource(array $dataSource)
    {
		return $dataSource;
		/*
	    $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		
        if (isset($dataSource['data']['items'])) {
			$entityIds = array();
			
            foreach ($dataSource['data']['items'] as & $item) {	
			     $entityIds[] = $item["entity_id"];
            }			
            $entityIdsData = implode(",", $entityIds); 
			$shop_name = $connection->fetchAll("SELECT sales_order.entity_id, racvportal.shop_name FROM sales_order LEFT JOIN racvportal ON (sales_order.increment_id = racvportal.incrementid) WHERE sales_order.entity_id in (".$entityIdsData.")");
				
			foreach ($dataSource['data']['items'] as & $item) {	
				 foreach($shop_name as $sn)
				 {
					 if($sn['entity_id'] == $item["entity_id"])
					 {
						 $item[$this->getData('name')] = $sn['shop_name'];
						 break;
					 }
				 }
            }	
        }
        return $dataSource;
		*/
    }
}