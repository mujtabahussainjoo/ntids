<?php

namespace Nwdthemes\Revslider\Model;

class Navigation extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface {
        
    const CACHE_TAG = 'nwdthemes_revslider_navigation';
 
    protected function _construct() {
        $this->_init('Nwdthemes\Revslider\Model\ResourceModel\Navigation');
    }
 
    public function getIdentities() {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

}