<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:42 PM
 */
namespace PL\Competition\Model\Resource\Competitor;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PL\Competition\Model\Competitor', 'PL\Competition\Model\Resource\Competitor');
        
    }

    /*
    public function getCopetitorCollection($competitionId='',$storeId=''){
        $select = $this->getSelect()->join(
            ['cp'=>$this->getTable('pl_competition')],
            'main_table.competition_id = cp.entity_id',
            []
        );
        $select->join(
            ['cps'=>$this->getTable('pl_compeition_store')],
            'cp.entity_id = cps.competition_id'
        );
        if($competitionId>0){
            $select->where(
                'cp.entity_id = ?',
                $competitionId
            );
        }
       if($storeId>0){
           $select->where(
               'cps.store_id IN (?)',
               $storeId
           );
       }


        return $select;
    }
    */
}