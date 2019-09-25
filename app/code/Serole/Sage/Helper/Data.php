<?php
/**
 * Created by Serole(Dk) on 05/09/2018.
 * includes the code related to system configuration of Sage Integration.
 */
 
namespace Serole\Sage\Helper;

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
		$this->createLog('sage_helper.log');
    }
	
	/*
     * @return string
     */
    public function getAPIUrl($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/apidetails/apiurl',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getAPIUsername($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/apidetails/apiuser',
            $scope
        );
    }

	
	/*
     * @return string
     */
    public function getAPIPassword($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/apidetails/apipassword',
            $scope
        );
    }

	
	/*
     * @return string
     */
    public function getMsSqlServer($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mssqldbdetails/msserver',
            $scope
        );
    }

   /*
     * @return string
     */
    public function getMsSqlUserName($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mssqldbdetails/msusername',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMsSqlPassword($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mssqldbdetails/mspassword',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMsSqlDatabase($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mssqldbdetails/msdatabase',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMsSqlPort($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mssqldbdetails/msport',
            $scope
        );
    }
	
		/*
     * @return string
     */
    public function getMySqlServer($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mysqldbdetails/myserver',
            $scope
        );
    }

   /*
     * @return string
     */
    public function getMySqlUserName($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mysqldbdetails/myusername',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMySqlPassword($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mysqldbdetails/mypassword',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMySqlDatabase($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mysqldbdetails/mydatabase',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getMySqlPort($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'sage/mysqldbdetails/myport',
            $scope
        );
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
	
	public function setValue($value){
		$this->_logger->info('Set:'.$value);
        $this->_session->start();
        $this->_session->setMessage($value);
		$this->_logger->info('Set1:'.$value);
     }
 
    public function getValue(){
        $this->_session->start();
        return $this->_session->getMessage();
    }
 
    public function unSetValue(){
        $this->_session->start();
        return $this->_session->unsMessage();
    }
	
  public function getProductById($id)
	{
		return $this->_productRepository->getById($id);
	}
	
   public function getProductBySku($sku)
    {
       return $this->_productRepository->get($sku);
    }

}