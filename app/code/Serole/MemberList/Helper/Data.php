<?php
/**
 * Created by Serole(Dk) on 10/07/2018.
 * includes the code related to system configuration of memberlist.
 */
 
namespace Serole\MemberList\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;
	
	
	protected $_storeManager;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        EncryptorInterface $encryptor
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
		$this->_storeManager = $storeManager;
    }

/*
     * @return boolean
     */
    public function isEnabled($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_yesno',
            $scope
        );
    }
	
	/*
     * @return boolean
     */
    public function isMemberNoRequired($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_member_no_required',
            $scope
        );
    }
	
	/*
     * @return boolean
     */
    public function isMemberNoValidate($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_member_no_validate',
            $scope
        );
    }
	
	/*
     * @return boolean
     */
    public function isFirstNameRequired($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_first_name_required',
            $scope
        );
    }
	
	/*
     * @return boolean
     */
    public function isLastNameRequired($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_last_name_required',
            $scope
        );
    }
	
	/*
     * @return boolean
     */
    public function isEmailRequired($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_email_required',
            $scope
        );
    }
	
	/*
     * @return string
     */
    public function getRequiredEmailFormat($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_email_format',
            $scope
        );
    }

    /*
     * @return string
     */
    public function getMemberNumberTitle($scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue(
            'member_list/general/ml_memberno_title',
            $scope
        );
    }
	
	public function getAllStoresData()
	{
		$data_array=array();
		
		$websites = $this->getWebsites();
		
		
		foreach($websites as $website)
		{
			$isEnabled =  $this->scopeConfig->getValue('member_list/general/ml_yesno', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $website->getCode());
			
			if($isEnabled)
			{
			    $data_array[$website->getId()]['name'] = $website->getName();
			    $data_array[$website->getId()]['code'] = $website->getCode();
			}
		}

		return($data_array);
	}
	
	public function getStoreArray()
	{
		$data_array=array();
		
		$websites = $this->getWebsites();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$accessibleWebsites = $objectManager->create('\Amasty\Rolepermissions\Model\Rule')->getPartiallyAccessibleWebsites();
		
		foreach($websites as $website)
		{
			
			$isEnabled =  $this->scopeConfig->getValue('member_list/general/ml_yesno', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $website->getCode());

			if($isEnabled && in_array($website->getId(),$accessibleWebsites))
			    $data_array[$website->getCode()] = $website->getName();
		}

		return($data_array);
	}
	
	public function getAccessibleWebsites()
	{
		$data_array=array();
		
		$websites = $this->getWebsites();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$accessibleWebsites = $objectManager->create('\Amasty\Rolepermissions\Model\Rule')->getPartiallyAccessibleWebsites();
		
		foreach($websites as $website)
		{
			
			if(in_array($website->getId(),$accessibleWebsites))
			    $data_array[] = $website->getCode();
		}

		return($data_array);
	}
	
	
	
	protected function getStores() {
		
      return $this->_storeManager->getStores(); 
	
    }
	
	protected function getWebsites() {
		
      return $this->_storeManager->getWebsites(); 
	
    }
	

}