<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:13 AM
 */

namespace PL\Competition\Model;


class Competition extends \Magento\Framework\Model\AbstractModel{

    const BASE_MEDIA_PATH = 'pl/competition/images';

    

    protected function _construct() {
        $this->_init('PL\Competition\Model\Resource\Competition');
    }

    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }




}