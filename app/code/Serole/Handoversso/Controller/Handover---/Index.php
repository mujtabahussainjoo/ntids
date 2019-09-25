<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Handover;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
	/* logging */
	protected $_logger;
	
	
	protected $_storeManager;
	
	   /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager

    ) 
	{
		$this->_storeManager = $storeManager;        
        parent::__construct($context);
    }
	
	/**
     * SSO integration for different store
     */
    public function execute()
    {
	 print_r($_GET);
    }
	
}