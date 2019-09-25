<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 2:18 AM
 */

namespace PL\Competition\Controller\Competition;
use PL\Competition\Controller\Competition;
class View extends Competition
{
    public function execute()
    {

        $competition_id = $this->getRequest()->getParam('id');
        $competition = $this->_competitionFactory->create()->load($competition_id);

        $this->_objectManager->get('Magento\Framework\Registry')
            ->register('competitionData', $competition);

        $pageFactory = $this->_pageFactory->create();
        $pageFactory->getConfig()->getTitle()->set($competition->getTitle());
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
                    'title' => __('Competition'),
                    'link' => $this->_url->getUrl($this->_competitionHelper->getPrefix())
                ]
            );
            $breadcrumbs->addCrumb('view',
                [
                    'label' => $competition->getTitle(),
                    'title' => $competition->getTitle()
                ]
            );
        }


        return $pageFactory;
    }
}