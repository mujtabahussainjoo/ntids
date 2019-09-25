<?php

namespace Serole\Itemsales\Block\Adminhtml;

class Sales extends \Magento\Backend\Block\Widget\Container
{

    protected $_template = 'itemsales/sales.phtml';


    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        $this->setChild('grid',$this->getLayout()->createBlock('Serole\Itemsales\Block\Adminhtml\Sales\Grid', 'itemsales.grid'));
        return parent::_prepareLayout();
    }


    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}