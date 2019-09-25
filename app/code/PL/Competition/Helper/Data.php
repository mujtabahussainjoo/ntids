<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:00 AM
 */

namespace PL\Competition\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;


class Data extends AbstractHelper{

    const PREFIX_URL    = 'competition';

    protected $_scopeConfig;

    protected $_objectManager;

    protected $_storeManager;


    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    public function getHeadTitle(){
        if($this->_scopeConfig->getValue('competition/general/meta_title',ScopeInterface::SCOPE_STORE)){
            return $this->_scopeConfig->getValue('competition/general/meta_title',ScopeInterface::SCOPE_STORE);
        }
        return 'Competition';
    }

    public function isBreadcrumbs(){
        if($this->_scopeConfig->getValue('competition/general/breadcrumbs',ScopeInterface::SCOPE_STORE)){
            return true;
        }
        return false;
    }

    public function isClosed($todate){
        $expriestime = strtotime($todate);
        $today = strtotime(date("Y-m-d"));
        if($expriestime > 0 && $expriestime < $today){
            return 1;
        }else{
            return 0;
        }
    }
    
    public function getPrefix(){
        if($this->_scopeConfig->getValue('competition/general/url_prefix',ScopeInterface::SCOPE_STORE)){
            return $this->_scopeConfig->getValue('competition/general/url_prefix',ScopeInterface::SCOPE_STORE);
        }
        return self::PREFIX_URL;
    }

    public function getCompetitionUrl(\PL\Competition\Model\Competition $competition){
        if ($competition->getUrlKey()){
            $urlKey = $this->getPrefix().'/'.$competition->getUrlKey();
            return $this->_getUrl('',['_direct'=>$urlKey]);
        }
        return $this->_getUrl('competition/view',['id'=>$competition->getEntityId()]);
    }

    public function getBaseUrlMedia($path = '', $secure = false)
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA, $secure) . $path;
    }

    public function getThumbnailList(\PL\Competition\Model\Competition $competition){
        $thumbData = array();
        if($competition->getData('thumb_1')){
            array_push($thumbData,$competition->getData('thumb_1'));
        }
        if($competition->getData('thumb_2')){
            array_push($thumbData,$competition->getData('thumb_2'));
        }
        if($competition->getData('thumb_3')){
            array_push($thumbData,$competition->getData('thumb_3'));
        }
        if($competition->getData('thumb_4')){
            array_push($thumbData,$competition->getData('thumb_4'));
        }
        if($competition->getData('thumb_5')){
            array_push($thumbData,$competition->getData('thumb_5'));
        }
        return $thumbData;

    }


    public function getImageUrl(\PL\Competition\Model\Competition $competition, $name='')
    {
        if($name!=""){
            return $this->getBaseUrlMedia($competition->getData($name));
        }

    }

    public function isDisclaimer(){
        if($this->_scopeConfig->getValue('competition/general/is_disclaimer',ScopeInterface::SCOPE_STORE)){
            return $this->_scopeConfig->getValue('competition/general/is_disclaimer',ScopeInterface::SCOPE_STORE);
        }
    }

    public function isDob(){
        if($this->_scopeConfig->getValue('competition/general/is_dob',ScopeInterface::SCOPE_STORE)){
            return $this->_scopeConfig->getValue('competition/general/is_dob',ScopeInterface::SCOPE_STORE);
        }
    }

    public function expiredDate($todate){
        $expriestime = strtotime($todate);
        $today = strtotime(date("Y-m-d"));
        if($expriestime > 0 && $expriestime < $today){
            return 1;
        }else{
            return 0;
        }
    }

   


    
}