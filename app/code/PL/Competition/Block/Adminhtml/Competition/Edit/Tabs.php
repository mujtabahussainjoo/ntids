<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:16 PM
 */
namespace PL\Competition\Block\Adminhtml\Competition\Edit;


class Tabs extends \Magento\Backend\Block\Widget\Tabs
{


    protected function _construct()
    {
        parent::_construct();
        $this->setId('competition_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));
    }

    protected function _beforeToHtml()
    {
        
        

        $this->addTab(
            'form_section',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'PL\Competition\Block\Adminhtml\Competition\Edit\Tab\Form'
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'meta_section',
            [
                'label' => __('Meta Information'),
                'title' => __('Meta Information'),
                'content' => $this->getLayout()->createBlock(
                    'PL\Competition\Block\Adminhtml\Competition\Edit\Tab\Meta'
                )->toHtml(),
                'active' => false
            ]
        );


        $this->addTab(
            'image_section',
            [
                'label' => __('Competition Images'),
                'title' => __('Competition Images'),
                'content' => $this->getLayout()->createBlock(
                    'PL\Competition\Block\Adminhtml\Competition\Edit\Tab\Image'
                )->toHtml(),
                'active' => false
            ]
        );


        $this->addTab(
            'competitor_section',
            [
                'label' => __('Competitors'),
                'title' => __('Competitors'),
                'content' => $this->getLayout()->createBlock(
                    'PL\Competition\Block\Adminhtml\Competition\Edit\Tab\Competitor'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}