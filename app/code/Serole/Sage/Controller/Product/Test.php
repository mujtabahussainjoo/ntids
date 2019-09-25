<?php
namespace Serole\Sage\Controller\Product;

class Test extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_inventory;
	

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Inventory $Inventory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_inventory = $Inventory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		echo "Hello<pre>";
		//$d = array ( '779,RAC-TRL-OD-OTH-001,1,1'); 
		//$x = $this->_inventory->stockUpdate($d);
		//print_r($x);
		//$data = array ( '2222,33333,HOY-MOV-ND-ADU-001,2',); WOL-GFC-ND-OTH-003
		//$data = array("HOY-MOV-ND-ADU-001");
		//$data = array('779,2100994,RAC-TRL-OD-OTH-001,1');
		//$x1 = $this->_inventory->getSerilaCodes($data);
		//print_r($x1);
		
		/*
		$data = array('WOL-GFC-ND-OTH-003'); 
		$x1 = $this->_inventory->getStockQty($data);
		print_r($x1);
		*/
		$x = array ('4089,8000000663,EVE-MOV-ND-ADU-020,1');
		//$x = array('4090,6400314880,VIL-MOV-ND-ADU-004,1');
		
		$x1 = $this->_inventory->getSerilaCodes($x);
		
		print_r($x1);
		
		
		//array ( '783,6400314758,HOY-MOV-ND-ADU-001,2')
        //return $this->resultPageFactory->create();
    }
}
?>