<?php

namespace Nwdthemes\Revslider\Model;

class Animation extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_animation';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\Animation');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    
}