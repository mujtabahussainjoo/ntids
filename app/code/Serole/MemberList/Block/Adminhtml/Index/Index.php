<?php
namespace Serole\MemberList\Block\Adminhtml\Index;

class Index extends \Magento\Backend\Block\Widget\Container
{
   protected $_storeManager;
	
   protected $_scopeConfig;

    public function __construct(
		\Magento\Backend\Block\Widget\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConf,
		array $data = []
	)
    {
		$this->_scopeConfig = $scopeConf;
		$this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
	
	public function getBaseUrl()
	{
		return $this->_storeManager->getStore()->getBaseUrl();
	}

}
