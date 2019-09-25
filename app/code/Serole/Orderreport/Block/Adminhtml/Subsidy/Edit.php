<?php
namespace Serole\Orderreport\Block\Adminhtml\Subsidy;

use Magento\Backend\Block\Widget\Form\Container;

class Edit extends Container
{

    protected $_coreRegistry = null;


    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    protected function _construct()
    {
        //$this->_objectId = 'entity_id';
        $this->_blockGroup = 'Serole_Orderreport';
        $this->_controller = 'adminhtml_subsidy';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Submit'));
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('subsidy/*/save', ['_current' => true, 'back' => 'subsidy', 'active_tab' => '']);
    }
}