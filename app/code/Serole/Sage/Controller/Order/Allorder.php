<?php


namespace Serole\Sage\Controller\Order;

class Allorder extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_exportOrder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Exportorder $exportOrder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_exportOrder = $exportOrder;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$date = date("Y-m-d");
		$this->_exportOrder->getMagentoOrders($date);
        return $this->resultPageFactory->create();
    }
}
