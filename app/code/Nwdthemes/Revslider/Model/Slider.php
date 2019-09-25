<?php

namespace Nwdthemes\Revslider\Model;

class Slider extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_slider';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\Slider');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}