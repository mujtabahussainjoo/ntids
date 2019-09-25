<?php


namespace Serole\Sage\Controller\Product;

class Price extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_priceImport;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Priceimport $Priceimport,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_priceImport = $Priceimport;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$this->_priceImport->getPriceFromSage();
        //return $this->resultPageFactory->create(); 
		exit;
    }
}
