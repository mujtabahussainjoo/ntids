<?php


namespace Serole\Sage\Controller\Product;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_itemsImport;
	
	protected $_bundleImport;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Itemimport $itemimport,
		\Serole\Sage\Model\Bundleimport $bundleimport,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_itemsImport = $itemimport;
		$this->_bundleImport = $bundleimport;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$this->_itemsImport->getItemsFromSage();
		echo "done";
		exit;
        //return $this->resultPageFactory->create();
    }
}
