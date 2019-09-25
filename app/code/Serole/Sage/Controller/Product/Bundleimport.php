<?php


namespace Serole\Sage\Controller\Product;

class Bundleimport extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_bundleImport;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Bundleimport $bundleimport,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
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
		echo "Bundleimport<br />";
		$this->_bundleImport->getItemsFromSage();
		exit;
        return $this->resultPageFactory->create();
    }
}
