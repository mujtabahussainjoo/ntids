<?php


namespace Serole\Sage\Controller\Product;

class Itemupdate extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
	protected $_itemUpdate;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Serole\Sage\Model\Itemupdate $Itemupdate,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_itemUpdate = $Itemupdate;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
		$this->_itemUpdate->getUpdatedItems();
        return $this->resultPageFactory->create(); 
    }
}
