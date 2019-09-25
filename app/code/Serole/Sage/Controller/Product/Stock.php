<?php
namespace Serole\Sage\Controller\Product;

class Stock extends \Magento\Framework\App\Action\Action
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
		\Serole\Sage\Model\Inventory $inventory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_inventory = $inventory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$sku = array();
		$sku[] = "EVE-MOV-ND-ADU-001";
		if(isset($sku) && !empty($sku[0]))
		{
			$result = $this->_inventory->getStockQty($sku);
			echo json_encode($result);
		}
		else
			echo "Provide the sku in parameter";
    }
	
}
