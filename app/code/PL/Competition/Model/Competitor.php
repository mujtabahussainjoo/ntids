<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:40 PM
 */
namespace PL\Competition\Model;


class Competitor extends \Magento\Framework\Model\AbstractModel{

    protected function _construct() {
        $this->_init('PL\Competition\Model\Resource\Competitor');
    }

}