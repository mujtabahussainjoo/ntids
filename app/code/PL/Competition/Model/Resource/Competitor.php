<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:41 PM
 */
namespace PL\Competition\Model\Resource;


class Competitor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pl_competition_competitor', 'competitor_id');
    }


}
