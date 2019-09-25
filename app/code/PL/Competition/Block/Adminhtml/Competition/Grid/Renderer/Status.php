<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/31/2016
 * Time: 4:36 PM
 */

namespace PL\Competition\Block\Adminhtml\Competition\Grid\Renderer;
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    public function render(\Magento\Framework\DataObject $row){
        $value =  $row->getData($this->getColumn()->getIndex());
        if($value==1){
            return __('Enabled');
        }
        return __('Disabled');
    }
}
