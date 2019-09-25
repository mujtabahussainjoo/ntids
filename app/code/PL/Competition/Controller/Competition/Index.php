<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 1:06 AM
 */

namespace PL\Competition\Controller\Competition;

class Index extends \PL\Competition\Controller\Competition
{

    public function execute()
    {

        $pageFactory = $this->_pageFactory->create();


        $pageFactory->getConfig()->getTitle()->set(
            $this->_competitionHelper->getHeadTitle()
        );


        if($this->_competitionHelper->isBreadcrumbs()){
            $breadcrumbs = $pageFactory->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link' => $this->_url->getUrl('')
                ]
            );
            $breadcrumbs->addCrumb('competition',
                [
                    'label' => __('Competition'),
                    'title' => __('Competition')
                ]
            );
        }


        return $pageFactory;
    }
}