<?php

namespace Nwdthemes\Revslider\Model;

class Slide extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_slide';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\Slide');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}