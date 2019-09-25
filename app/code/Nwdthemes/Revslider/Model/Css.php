<?php

namespace Nwdthemes\Revslider\Model;

class Css extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_css';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\Css');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}