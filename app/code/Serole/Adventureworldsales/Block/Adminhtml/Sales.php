<?php

namespace Serole\Adventureworldsales\Block\Adminhtml;

class Sales extends \Magento\Backend\Block\Widget\Container
{

    protected $_template = 'adventureworldsales/adventureworldsales.phtml';


    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Serole\Adventureworldsales\Block\Adminhtml\Sales\Grid', 'adventureworldsales.grid')
        );
        return parent::_prepareLayout();
    }


    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}