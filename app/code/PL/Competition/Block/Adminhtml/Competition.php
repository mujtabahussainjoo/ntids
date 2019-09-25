<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:23 AM
 */
namespace PL\Competition\Block\Adminhtml;

class Competition extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_competition';
        $this->_blockGroup = 'PL_Competition';
        $this->_headerText = __('Competition');
        $this->_addButtonLabel = __('Add New Competition');
        parent::_construct();
    }
}
