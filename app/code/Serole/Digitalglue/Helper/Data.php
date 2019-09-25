<?php
/**
 * Created by Serole(Dk) on 20/08/2019.
 * includes the code related to system configuration of Sage Integration.
 */
 
namespace Serole\Digitalglue\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
	protected $_storeManager;
	
	protected $_logger;
	
	protected $_session;
	
	protected $_productRepository;

    /**
     * @param Context $context
     * 
     */
    public function __construct(
        Context $context,
		\Magento\Framework\Session\SessionManagerInterface $session,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
		$this->_storeManager = $storeManager;
		$this->_session = $session;
		$this->_productRepository = $productRepository;
		$this->createLog('digitalglue_helper.log');
    }
	
	/*
     * @return string
     */
    public function getAPIUrl($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'digitalglue/apidetails/apiurl',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getAPIUsername($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'digitalglue/apidetails/apiuser',
            $scope
        );
    }

	
	/*
     * @return string
     */
    public function getAPIPassword($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'digitalglue/apidetails/apipassword',
            $scope
        );
    }

	public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}
	
    public function getProductBySku($sku)
    {
       return $this->_productRepository->get($sku);
    }
	
	
	/*
	 * @Writing log
     */
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}

}