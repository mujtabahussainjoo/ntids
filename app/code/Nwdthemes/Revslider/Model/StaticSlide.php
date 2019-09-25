<?php

namespace Nwdthemes\Revslider\Model;

class StaticSlide extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_static_slide';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\StaticSlide');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}