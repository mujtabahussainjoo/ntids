<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/31/2016
 * Time: 5:00 PM
 */

namespace PL\Competition\Controller\Adminhtml\Competition;

use Magento\Framework\App\Filesystem\DirectoryList;

class Drawlots extends \PL\Competition\Controller\Adminhtml\Competition{



    public function execute(){

        $competition_id = $this->getRequest()->getParam('entity_id');
        $competition = $this->_competitionFactory->create()->load($competition_id);

        if(!$this->compHelper->expiredDate($competition->getDateTo())){
            $this->messageManager->addError(__('Draw lots subscriber is unavailable'));
            $this->_redirect('*/*/');
            return;
        }


        $reset =  $this->_competitorFactory->create()->getCollection();
        $reset->addFieldToFilter('competition_id',$competition_id);
        $reset->addFieldToFilter('winner',1);

        foreach ($reset as $re){
            $recollection = $this->_competitorFactory->create();
            $recollection->load($re->getCompetitorId());
            $recollection->setWinner(0);
            $recollection->save();
        }

        //echo $competition->getNumberOfWinners(); exit;
        $competitor = $this->_competitorFactory->create();
        $collection = $competitor->getCollection()
            ->addFieldToFilter('competition_id',$competition_id);
        $collection->getSelect()->order(new \Zend_Db_Expr('RAND()'));
        $collection->setPageSize($competition->getNumberOfWinners());

        foreach ($collection as $data){
            $competitor->load($data->getCompetitorId());
            $competitor->setWinner(1);
            $competitor->save();
        }
        $this->messageManager->addSuccess(__('Draw lots subscriber successfully'));
        $this->_redirect($this->_redirect->getRefererUrl());
        return;
        //$this->_redirect('*/*/');

        
        

        
    }
}