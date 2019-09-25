<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:14 PM
 */

namespace PL\Competition\Block\Adminhtml\Competition;


class Edit extends \Magento\Backend\Block\Widget\Form\Container{

    protected $_coreRegistry = null;

    protected $compHelper;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \PL\Competition\Helper\Data $compHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->compHelper = $compHelper;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_controller = 'adminhtml_competition';
        $this->_blockGroup = 'PL_Competition';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));

        $this->buttonList->update('delete', 'label', __('Delete'));

        $competition = $this->getModel();
        if($this->compHelper->expiredDate($competition->getDateTo())==1){
            $this->buttonList->add('draw_lots',[
                'label'=> __('Draw Lots Now'),
                'onclick'=>'setLocation(\'' . $this->getDrawlotsUrl() . '\')',
                'class'        => 'go',
            ],-100);
        }

    }


    public function getModel()
    {
        return $this->_coreRegistry->registry('_current_competition');
    }



    protected function _prepareLayout(){

        return parent::_prepareLayout();

    }

    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return __('Edit Competition');
        }

        return __('New Competition');
    }

    public function getEditMode()
    {
        if ($this->getModel()->getEntityId()) {
            return true;
        }
        return false;
    }


    public function getDrawlotsUrl(){
        return $this->getUrl('*/*/drawlots', array('_current'=>true));
    }


}